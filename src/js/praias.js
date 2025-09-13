// Gerado pelo Copilot
// Script para página de praias monitoradas

const API_URL = "https://caravana-ciencia.onrender.com/api/praias.php";

document.addEventListener("DOMContentLoaded", () => {
  const container = document.getElementById("praiasContainer");
  const searchInput = document.getElementById("searchPraia");
  let praias = [];

  buscarPraias();
  searchInput.addEventListener("input", filtrarPraias);

  // Busca as praias do backend
  function buscarPraias() {
    fetch(API_URL)
      .then(res => res.json())
      .then(data => {
        praias = data;
        renderizarPraias(praias);
      })
      .catch(err => {
        mostrarErro("Erro ao carregar praias: " + err);
      });
  }

  // Filtra as praias pelo termo digitado
  function filtrarPraias(e) {
    const termo = e.target.value.toLowerCase();
    const filtradas = praias.filter(praia => praia.nome.toLowerCase().includes(termo));
    renderizarPraias(filtradas);
  }

  // Renderiza os cards das praias
  function renderizarPraias(lista) {
    container.innerHTML = "";
    if (lista.length === 0) {
      container.innerHTML = `<p class="text-center text-muted">Nenhuma praia encontrada 😕</p>`;
      return;
    }
    lista.forEach(praia => container.appendChild(criarCardPraia(praia)));
  }

  // Cria o card de uma praia
  function criarCardPraia(praia) {
    const col = document.createElement("div");
    col.className = "col-12 col-md-4";
    col.innerHTML = `
      <div class="card praia-card shadow-sm">
        <div class="card-background" style="background-image: url('${praia.imagem || 'https://imgmd.net/images/v1/guia/1611884/praia-vermelha-do-sul.jpg'}');">
          <div class="card-overlay">
            <h5 class="card-title">${praia.nome}</h5>
            <p class="card-text">${praia.descricao || "Sem descrição."}</p>
            <span class="badge bg-primary">Turbidez: ${praia.taxa_turbidez ?? "N/A"}</span>
          </div>
        </div>
      </div>
    `;
    return col;
  }

  // Exibe mensagem de erro
  function mostrarErro(msg) {
    container.innerHTML = `<div class="alert alert-danger text-center">${msg}</div>`;
  }
});
