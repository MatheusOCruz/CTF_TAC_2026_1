// console.log("pw.js carregado");

document.addEventListener("DOMContentLoaded", () => {
  const potionLinks = document.querySelectorAll(".potion-item a");

  potionLinks.forEach((link) => {
    link.addEventListener("click", async (event) => {
      event.preventDefault();

      const url = new URL(link.href);
      const potionId = url.searchParams.get("t");

      // console.log("Poção clicada:", potionId);

      try {
        const response = await fetch(`http://repo:3000/api/potion/${potionId}`, {
          method: "GET",
        });

        const data = await response.json();

        // console.log("Resposta do backend:", data);

        if (!data.message) {
          // console.error("Backend não retornou data.message:", data);
          alert("Resposta inválida do backend.");
          return;
        }

        if (response.ok && data.status === "ok") {
          window.location.href = `http://academy.repo/repo_bureau/${data.message}`;
          return;
        }

        window.location.href = "http://academy.repo/";
      } catch (error) {
        // console.error("Erro ao validar a poção:", error);
        alert("Erro ao comunicar com o backend.");
      }
    });
  });
});