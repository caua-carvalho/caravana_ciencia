// Script para página de praias monitoradas

const API_BASE      = "https://caravana-ciencia.onrender.com/";

const API_PRAIAS    = API_BASE + "api/praias.php";
const API_HISTORICO = API_BASE + "api/historico_turbidez.php";


document.addEventListener("DOMContentLoaded", () => {
  const container = document.getElementById("praiasContainer");
  const graficoTitulo = document.getElementById("graficoTitulo");
  const graficoCanvas = document.getElementById("graficoTurbidez");
  let chartInstance = null;
  let praias = [];

  buscarPraias();
  setInterval(buscarPraias, 10000);

  // Busca as praias do backend
  function buscarPraias() {
    fetch(API_PRAIAS)
      .then(res => res.json())
      .then(data => {
        praias = data;
        renderizarPraias(praias);
      })
      .catch(err => {
        mostrarErro("Erro ao carregar praias: " + err);
      });
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
      <div class="card praia-card shadow-sm" style="cursor:pointer">
        <div class="card-background" style="background-image: url('${null || 'https://imgmd.net/images/v1/guia/1611884/praia-vermelha-do-sul.jpg'}');">
          <div class="card-overlay">
            <h5 class="card-title">${praia.nome}</h5>
            <p class="card-text">${praia.descricao || "Sem descrição."}</p>
            <span class="badge bg-primary">Turbidez: ${praia.turbidez_valor ?? "N/A"}</span>
          </div>
        </div>
      </div>
    `;
    // Ao clicar, mostra o gráfico
    col.querySelector('.praia-card').addEventListener('click', () => mostrarGraficoPraia(praia));
    return col;
  }

  // Mostra o gráfico de turbidez da última semana para a praia
  function mostrarGraficoPraia(praia) {
    graficoTitulo.textContent = `Histórico de Turbidez - ${praia.nome}`;
    graficoTitulo.style.display = '';
    graficoCanvas.style.display = '';

    // Data inicial: 7 dias atrás
    const dataInicial = new Date();
    dataInicial.setDate(dataInicial.getDate() - 7);
    const dataInicialStr = dataInicial.toISOString().slice(0, 19).replace('T', ' ');

    fetch(API_HISTORICO, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id_praia: praia.id, data_inicial: dataInicialStr })
    })
      .then(res => res.json())
      .then(json => {
        if (json.status !== 'sucesso') throw new Error('Erro na API');
        const dados = json.dados.reverse(); // ordem cronológica
        const labels = dados.map(d => new Date(d.data_medicao).toLocaleString('pt-BR', { day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' }));
        const valores = dados.map(d => Number(d.valor));
        atualizarGrafico(labels, valores);
      })
      .catch(() => {
        atualizarGrafico([], []);
      });
  }

  // Atualiza ou cria o gráfico Chart.js
  function atualizarGrafico(labels, valores) {
    if (chartInstance) chartInstance.destroy();
    chartInstance = new Chart(graficoCanvas, {
      type: 'line',
      data: {
        labels,
        datasets: [{
          label: 'Turbidez (NTU)',
          data: valores,
          borderColor: '#007bff',
          backgroundColor: 'rgba(0,123,255,0.1)',
          fill: true,
          tension: 0.3,
          pointRadius: 4,
          pointBackgroundColor: '#007bff',
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: true },
          tooltip: { enabled: true }
        },
        scales: {
          y: {
            beginAtZero: true,
            title: { display: true, text: 'NTU' }
          },
          x: {
            title: { display: true, text: 'Data/Hora' }
          }
        }
      }
    });
  }

  // Exibe mensagem de erro
  function mostrarErro(msg) {
    container.innerHTML = `<div class="alert alert-danger text-center">${msg}</div>`;
  }
});
