import json
import sys
from datetime import datetime, date
from ortools.sat.python import cp_model


def parse_date(s: str) -> date:
    return datetime.strptime(s, "%Y-%m-%d").date()


def days_between(d1: date, d2: date) -> int:
    # d2 - d1 in days
    return (d2 - d1).days


def main():
    payload = json.loads(sys.stdin.read())

    base_date = parse_date(payload["base_date"])
    slots = payload["slots"]  # list of slot_id
    orders = payload["orders"]  # list of dict

    m = len(slots)
    n = len(orders)

    # Horizon: safe upper bound
    total_p = sum(max(1, int(o["process_days"])) for o in orders)
    horizon = max(1, total_p + 30)

    model = cp_model.CpModel()

    # Per job:
    starts = []
    ends = []
    tard = []
    release = []
    due = []
    p = []

    for o in orders:
        p_i = max(1, int(o["process_days"]))
        p.append(p_i)

        order_date = parse_date(o["order_date"])
        due_date = parse_date(o["due_date"])

        r_i = max(0, days_between(base_date, order_date))
        d_i = days_between(base_date, due_date)

        release.append(r_i)
        due.append(d_i)

        s = model.NewIntVar(0, horizon, f"start_{o['order_id']}")
        e = model.NewIntVar(0, horizon, f"end_{o['order_id']}")
        t = model.NewIntVar(0, horizon, f"tard_{o['order_id']}")

        model.Add(e == s + p_i)
        model.Add(s >= r_i)

        # tardiness = max(0, end - due)
        # end - due <= tard  and tard >= 0 already, also tard >= end - due
        model.Add(t >= e - d_i)
        model.Add(t >= 0)

        starts.append(s)
        ends.append(e)
        tard.append(t)

    # Machine assignment with optional intervals
    machine_intervals = [[] for _ in range(m)]
    assign = [[None]*m for _ in range(n)]

    for i, o in enumerate(orders):
        for k in range(m):
            b = model.NewBoolVar(f"assign_{i}_{k}")
            assign[i][k] = b
            interval = model.NewOptionalIntervalVar(
                starts[i], p[i], ends[i], b, f"int_{i}_{k}"
            )
            machine_intervals[k].append(interval)

        model.Add(sum(assign[i][k] for k in range(m)) == 1)

    for k in range(m):
        model.AddNoOverlap(machine_intervals[k])

    # Objective: minimize total tardiness
    model.Minimize(sum(tard))

    solver = cp_model.CpSolver()
    solver.parameters.max_time_in_seconds = float(payload.get("time_limit_sec", 10))
    solver.parameters.num_search_workers = int(payload.get("workers", 8))

    status = solver.Solve(model)

    if status not in (cp_model.OPTIMAL, cp_model.FEASIBLE):
        out = {"ok": False, "message": "No feasible schedule found", "results": []}
        print(json.dumps(out))
        return

    results = []
    for i, o in enumerate(orders):
        chosen_k = None
        for k in range(m):
            if solver.Value(assign[i][k]) == 1:
                chosen_k = k
                break

        s = int(solver.Value(starts[i]))
        e = int(solver.Value(ends[i]))
        t = int(solver.Value(tard[i]))

        results.append({
            "order_id": o["order_id"],
            "decision": "PRODUCE",
            "slot_id": slots[chosen_k],
            "start_day": s,
            "finish_day": e,
            "tardiness_days": t,
            "reason": None if t == 0 else "Tardy vs due date"
        })

    out = {
        "ok": True,
        "base_date": payload["base_date"],
        "objective_total_tardiness": sum(r["tardiness_days"] for r in results),
        "results": results
    }
    print(json.dumps(out))


if __name__ == "__main__":
    main()
