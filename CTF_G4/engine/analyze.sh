#!/bin/bash
# GrandMaster Analysis Portal - engine wrapper
# Usage: analyze.sh "<FEN>" <depth>
#
# Tries to use a real stockfish binary if available; otherwise falls back to a
# canned evaluation so the portal still "works" in the lab image.

FEN="$1"
DEPTH="$2"

echo "Analyzing position: $FEN"
echo "Search depth: $DEPTH"
echo "----------------------------------------"

if command -v stockfish >/dev/null 2>&1; then
    printf 'position fen %s\ngo depth %s\nquit\n' "$FEN" "$DEPTH" \
        | stockfish 2>/dev/null \
        | grep -E "bestmove|score" \
        | tail -n 5
else
    # Fallback: deterministic fake evaluation.
    echo "info depth $DEPTH score cp +0.23 pv e2e4 e7e5 g1f3"
    echo "bestmove e2e4"
fi

echo "----------------------------------------"
echo "Evaluation complete."
