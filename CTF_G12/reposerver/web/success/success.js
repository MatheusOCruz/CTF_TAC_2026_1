const logoutBtn = document.getElementById("logout");

logoutBtn.addEventListener("click", async () => {
  try {
    const res = await fetch("http://repo:3000/api/logout", {
      method: "POST",
      credentials: "include",
    })}
    catch (err) {
      console.error(err);
      msg.classList.add("error");
    } finally {
    window.location.href = "/login/login.html";
  }
});

setTimeout(() => {
  const token = sessionStorage.getItem("repo_token");
  window.location.href = token
    ? `http://academy.repo/bridge.html?token=${encodeURIComponent(token)}`
    : "http://academy.repo/";
}, 1500)