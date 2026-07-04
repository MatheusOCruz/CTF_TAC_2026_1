#!/usr/bin/env python3
"""GrandMaster club report generator.

Run by cron as root. Reads the recorded club games, computes the member
standings, and writes the weekly report. If an optional analytics plugin named
``chess_analytics`` is present in this directory it is loaded to append an
advanced-stats section:

    chess_analytics.augment(standings) -> list[str]   # extra report lines

NOTE: this file is root-owned and not writable by members.
"""

import json
import os
from datetime import datetime, timezone

BASE = os.path.dirname(os.path.abspath(__file__))
GAMES_FILE = "/opt/chess/data/games.json"
REPORT_FILE = os.path.join(BASE, "weekly_report.txt")
REPORT_JSON = os.path.join(BASE, "weekly_report.json")
LOG_FILE = "/var/log/chess-report.log"


def load_games():
    try:
        with open(GAMES_FILE) as fh:
            return json.load(fh)
    except (OSError, ValueError):
        return []


def compute_standings(games):
    table = {}
    for g in games:
        white, black, result = g.get("white"), g.get("black"), g.get("result")
        for player in (white, black):
            if player:
                table.setdefault(player, {"games": 0, "wins": 0, "draws": 0,
                                          "losses": 0, "pts": 0.0})
        if not (white and black):
            continue
        table[white]["games"] += 1
        table[black]["games"] += 1
        if result == "1-0":
            table[white]["wins"] += 1
            table[white]["pts"] += 1.0
            table[black]["losses"] += 1
        elif result == "0-1":
            table[black]["wins"] += 1
            table[black]["pts"] += 1.0
            table[white]["losses"] += 1
        else:  # draw
            table[white]["draws"] += 1
            table[black]["draws"] += 1
            table[white]["pts"] += 0.5
            table[black]["pts"] += 0.5
    return sorted(table.items(), key=lambda kv: (-kv[1]["pts"], kv[0]))


def build_report(standings):
    now = datetime.now(timezone.utc).strftime("%Y-%m-%d %H:%M:%S UTC")
    lines = [
        "GrandMaster Chess Club - Weekly Standings",
        "Generated: %s" % now,
        "-" * 52,
        "%-4s %-14s %5s %4s %4s %4s %6s"
        % ("#", "Member", "Games", "W", "D", "L", "Pts"),
    ]
    for rank, (name, s) in enumerate(standings, 1):
        lines.append("%-4d %-14s %5d %4d %4d %4d %6.1f"
                     % (rank, name, s["games"], s["wins"], s["draws"],
                        s["losses"], s["pts"]))
    return lines


def main():
    games = load_games()
    standings = compute_standings(games)
    now = datetime.now(timezone.utc).strftime("%Y-%m-%d %H:%M:%S UTC")

    rows = []
    for rank, (name, s) in enumerate(standings, 1):
        rows.append({
            "rank": rank,
            "member": name,
            "games": s["games"],
            "wins": s["wins"],
            "draws": s["draws"],
            "losses": s["losses"],
            "points": s["pts"],
        })

    analytics = []
    # Optional analytics plugin, auto-loaded from this directory (sys.path[0])
    # when the stats team drops one in.
    try:
        import chess_analytics
        extra = chess_analytics.augment(standings)
        if extra:
            analytics = [str(item) for item in extra]
    except Exception:
        pass

    data = {
        "generated": now,
        "games": len(games),
        "standings": rows,
        "analytics": analytics,
    }

    # Canonical machine-readable report (consumed by the web interface).
    try:
        with open(REPORT_JSON, "w") as fh:
            json.dump(data, fh, indent=2)
            fh.write("\n")
    except OSError:
        pass

    # Plain-text copy for quick reading on the host.
    lines = build_report(standings)
    if analytics:
        lines.append("")
        lines.append("-- advanced analytics --")
        lines.extend(analytics)
    try:
        with open(REPORT_FILE, "w") as fh:
            fh.write("\n".join(lines) + "\n")
    except OSError:
        pass

    stamp = datetime.now(timezone.utc).strftime("%Y-%m-%d %H:%M:%S")
    with open(LOG_FILE, "a") as fh:
        fh.write("[%s] report generated (%d members ranked)\n"
                 % (stamp, len(standings)))


if __name__ == "__main__":
    main()
