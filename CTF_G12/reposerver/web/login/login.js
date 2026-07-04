const uidInput = document.getElementById("unit_id");
const keyInput = document.getElementById("access_key");
const goBtn = document.getElementById("go");
const msg = document.getElementById("msg");

goBtn.addEventListener("click", async () => {
  msg.textContent = "Authenticating...";

  try {
    const res = await fetch("http://repo:3000/api/login", {
      method: "POST",
      credentials: "include",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        unit_id: uidInput.value.trim(),
        access_key: keyInput.value.trim(),
      }),
    });

    const data = await res.json();
    msg.textContent = data.message ?? "Erro ao autenticar.";


    if (res.ok && data.status === "ok") {
      msg.classList.add("correct");
      if (data.token) sessionStorage.setItem("repo_token", data.token);
      window.location.href = "/success/success.html";
    } else {
      msg.classList.add("error");
    }

  } catch (err) {
    msg.textContent = "Falha de conexão com o servidor.";
    msg.classList.remove("correct");
    msg.classList.add("error");
  }
});
