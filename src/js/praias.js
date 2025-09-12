const API_URL = "https://caravana-ciencia.onrender.com/src/api/praias.php"; // ajuste p/ seu endpoint

document.addEventListener("DOMContentLoaded", () => {
  const container = document.getElementById("praiasContainer");
  const searchInput = document.getElementById("searchPraia");

  let praias = [];

  // Buscar praias do backend
  fetch(API_URL)
    .then(res => res.json())
    .then(data => {
      praias = data;
      renderPraias(praias);
    })
    .catch(err => {
      container.innerHTML = `<div class="alert alert-danger">Erro ao carregar praias: ${err}</div>`;
    });

  // Filtrar ao digitar
  searchInput.addEventListener("input", (e) => {
    const termo = e.target.value.toLowerCase();
    const filtradas = praias.filter(p =>
      p.nome.toLowerCase().includes(termo)
    );
    renderPraias(filtradas);
  });

  // Renderizar cards
  function renderPraias(lista) {
    container.innerHTML = "";
    if (lista.length === 0) {
      container.innerHTML = `<p class="text-center text-muted">Nenhuma praia encontrada 😕</p>`;
      return;
    }

    lista.forEach(praia => {
      const col = document.createElement("div");
      col.className = "col-md-4";

      col.innerHTML = `
        <div class="card h-100 shadow-sm">
          <img src="${praia.foto || 'https://source.unsplash.com/400x250/?beach'}" class="card-img-top" alt="${praia.nome}">
          <div class="card-body">
            <h5 class="card-title">${praia.nome}</h5>
            <p class="card-text">${praia.descricao || "Sem descrição."}</p>
          </div>
          <div class="card-footer text-center">
            <span class="badge bg-primary">Turbidez: ${praia.taxa_turbidez ?? "N/A"}</span>
          </div>
        </div>
      `;
      container.appendChild(col);
    });
  }
});
