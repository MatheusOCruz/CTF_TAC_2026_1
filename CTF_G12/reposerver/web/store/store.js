const STORAGE_KEY = "repo-store-solved";
const API_BASE = "http://repo:3000/api";

function loadSolved() {
  try {
    return new Set(JSON.parse(localStorage.getItem(STORAGE_KEY) || "[]"));
  } catch {
    return new Set();
  }
}

function saveSolved(solved) {
  localStorage.setItem(STORAGE_KEY, JSON.stringify(Array.from(solved)));
}

async function validateAnswer(id, answer) {
  const res = await fetch(`${API_BASE}/validate/${encodeURIComponent(id)}`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      ans: answer,
    }),
  });

  const data = await res.json().catch(() => ({}));

  if (!res.ok || data.status !== "ok") {
    throw new Error(data.message || "WRONG.");
  }

  return data;
}

document.addEventListener("DOMContentLoaded", () => {
  const rows = Array.from(document.querySelectorAll(".flag-row[data-id]"));
  const countEl = document.getElementById("mission-count");
  const solved = loadSolved();

  function updateCount() {
    if (countEl) {
      countEl.textContent = `${solved.size}/${rows.length}`;
    }
  }

  function markSolved(row, id) {
    const input = row.querySelector(".flag-input");
    const submitBtn = row.querySelector(".submit-btn");

    row.classList.add("solved");
    row.classList.remove("wrong");

    if (input) {
      input.value = "";
      input.placeholder = "solved";
      input.disabled = true;
    }

    if (submitBtn) {
      submitBtn.disabled = true;
    }

    solved.add(id);
    saveSolved(solved);
    updateCount();
  }

  function markWrong(row) {
    row.classList.add("wrong");
    setTimeout(() => row.classList.remove("wrong"), 250);
  }

  async function submitRow(row) {
    if (row.classList.contains("solved")) return;

    const id = row.dataset.id;
    const input = row.querySelector(".flag-input");
    const submitBtn = row.querySelector(".submit-btn");

    if (!id || !input) return;

    const answer = input.value.trim();

    if (!answer) return;

    row.classList.add("checking");
    if (submitBtn) submitBtn.disabled = true;

    try {
      await validateAnswer(id, answer);
      markSolved(row, id);
    } catch {
      markWrong(row);

      if (submitBtn) {
        submitBtn.disabled = false;
      }
    } finally {
      row.classList.remove("checking");
    }
  }

  rows.forEach((row) => {
    const id = row.dataset.id;
    const input = row.querySelector(".flag-input");
    const submitBtn = row.querySelector(".submit-btn");
    const hintBtn = row.querySelector(".hint-btn");
    const hintBox = row.querySelector(".hint-box");

    if (solved.has(id)) {
      markSolved(row, id);
    }

    if (submitBtn) {
      submitBtn.addEventListener("click", () => {
        submitRow(row);
      });
    }

    if (input) {
      input.addEventListener("keydown", (e) => {
        if (e.key === "Enter") {
          submitRow(row);
        }
      });
    }

    if (hintBtn && hintBox) {
      hintBtn.addEventListener("click", () => {
        const isOpen = !hintBox.hidden;

        document.querySelectorAll(".hint-box").forEach((box) => {
          box.hidden = true;
        });

        document.querySelectorAll(".hint-btn").forEach((btn) => {
          btn.classList.remove("active");
        });

        if (!isOpen) {
          hintBox.hidden = false;
          hintBox.textContent = hintBtn.dataset.hint || "";
          hintBtn.classList.add("active");
        }
      });
    }
  });

  updateCount();
});