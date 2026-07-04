// GrandMaster Analysis Portal - lightweight FEN board renderer.
// Parses the piece-placement field of a FEN string and draws an 8x8 board
// using Unicode chess glyphs. No external dependencies.

const PIECES = {
  K: "♔", Q: "♕", R: "♖", B: "♗", N: "♘", P: "♙",
  k: "♚", q: "♛", r: "♜", b: "♝", n: "♞", p: "♟"
};

const FILES = ["a", "b", "c", "d", "e", "f", "g", "h"];

// Returns an 8x8 array [rank0..rank7][file0..file7], rank0 = 8th rank (top).
function parseFen(fen) {
  const placement = (fen || "").trim().split(/\s+/)[0] || "";
  const rows = placement.split("/");
  const board = [];
  for (let r = 0; r < 8; r++) {
    const row = new Array(8).fill("");
    const spec = rows[r] || "";
    let file = 0;
    for (const ch of spec) {
      if (file >= 8) break;
      if (/[1-8]/.test(ch)) {
        file += parseInt(ch, 10);
      } else if (PIECES[ch]) {
        row[file] = ch;
        file += 1;
      }
    }
    board.push(row);
  }
  return board;
}

// Optionally highlight from/to squares, e.g. "e2", "e4".
function renderBoard(containerId, fen, highlight) {
  const el = document.getElementById(containerId);
  if (!el) return;
  const board = parseFen(fen);
  const hi = new Set((highlight || []).filter(Boolean));

  let squares = "";
  for (let r = 0; r < 8; r++) {
    for (let f = 0; f < 8; f++) {
      const square = FILES[f] + (8 - r);
      const dark = (r + f) % 2 === 1;
      const piece = board[r][f];
      const classes = ["sq", dark ? "dark" : "light"];
      if (hi.has(square)) classes.push("hi");
      const glyph = piece ? `<span class="piece">${PIECES[piece]}</span>` : "";
      squares += `<div class="${classes.join(" ")}" data-sq="${square}">${glyph}</div>`;
    }
  }

  // Frame: rank labels (8..1) on the left, board, file labels (a..h) below.
  const ranks = [8, 7, 6, 5, 4, 3, 2, 1]
    .map(n => `<span>${n}</span>`).join("");
  const files = FILES.map(f => `<span>${f}</span>`).join("");

  el.innerHTML =
    '<div class="board-frame">' +
      `<div class="ranks">${ranks}</div>` +
      `<div class="chessboard">${squares}</div>` +
      '<div class="corner"></div>' +
      `<div class="files">${files}</div>` +
    "</div>";
}

// Parse a UCI move like "e2e4" -> ["e2","e4"] for highlighting.
function uciToSquares(move) {
  const m = /^([a-h][1-8])([a-h][1-8])/.exec((move || "").trim());
  return m ? [m[1], m[2]] : [];
}

// Wire up a live-updating board tied to a FEN <input>.
function liveBoard(containerId, inputId) {
  const input = document.getElementById(inputId);
  if (!input) return;
  const update = () => renderBoard(containerId, input.value, []);
  input.addEventListener("input", update);
  update();
}
