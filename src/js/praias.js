// Script para página de praias monitoradas

const API_BASE      = "https://caravana-ciencia.onrender.com/";

const API_PRAIAS    = API_BASE + "api/praias.php";
const API_HISTORICO = API_BASE + "api/historico_turbidez.php";


document.addEventListener("DOMContentLoaded", () => {
  const container = document.getElementById("praiasContainer");
  // Modal elements
  const modal = new bootstrap.Modal(document.getElementById('modalPraia'));
  const modalNome = document.getElementById('modalPraiaNome');
  const modalDescricao = document.getElementById('modalPraiaDescricao');
  const modalFoto = document.getElementById('modalPraiaFoto');
  const modalTurbidez = document.getElementById('modalPraiaTurbidez');
  const graficoCanvas = document.getElementById('graficoTurbidez');
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
        <div class="card-background" style="background-image: url('${praia.foto || 'https://imgmd.net/images/v1/guia/1611884/praia-vermelha-do-sul.jpg'}');">
          <div class="card-overlay">
            <h5 class="card-title">${praia.nome}</h5>
            <p class="card-text">${praia.descricao || "Sem descrição."}</p>
            <span class="badge bg-primary">Turbidez: ${praia.turbidez_valor ?? "N/A"}</span>
          </div>
        </div>
      </div>
    `;
    // Ao clicar, abre modal com info e gráfico
    col.querySelector('.praia-card').addEventListener('click', () => abrirModalPraia(praia));
    return col;
  }


  // Abre o modal e exibe info + gráfico
  function abrirModalPraia(praia) {
    modalNome.textContent = praia.nome;
    modalDescricao.textContent = praia.descricao || '';
    modalFoto.src = praia.foto || 'https://imgmd.net/images/v1/guia/1611884/praia-vermelha-do-sul.jpg';
    modalTurbidez.textContent = `Turbidez: ${praia.turbidez_valor ?? 'N/A'}`;
    modalTurbidez.className = 'badge';
    // Cor badge
    const v = Number(praia.turbidez_valor);
    if (!isNaN(v)) {
      if (v <= 5) modalTurbidez.classList.add('bg-success');
      else if (v <= 25) modalTurbidez.classList.add('bg-success','text-dark');
      else if (v <= 50) modalTurbidez.classList.add('bg-warning','text-dark');
      else if (v <= 100) modalTurbidez.classList.add('bg-warning');
      else if (v <= 500) modalTurbidez.classList.add('bg-danger');
      else if (v <= 1000) modalTurbidez.classList.add('bg-dark');
      else modalTurbidez.classList.add('bg-secondary');
    } else {
      modalTurbidez.classList.add('bg-secondary');
    }
    // Gráfico
    carregarGraficoPraia(praia.id);
    modal.show();
  }

  // Busca e mostra o gráfico de turbidez da última semana para a praia
  function carregarGraficoPraia(idPraia) {
    // Data inicial: 7 dias atrás
    const dataInicial = new Date();
    dataInicial.setDate(dataInicial.getDate() - 7);
    const dataInicialStr = dataInicial.toISOString().slice(0, 19).replace('T', ' ');

    fetch(API_HISTORICO, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id_praia: idPraia, data_inicial: dataInicialStr })
    })
      .then(res => res.json())
      .then(json => {
        if (json.status !== 'sucesso') throw new Error('Erro na API');
        const dados = json.dados.reverse(); // ordem cronológica
        // Exibe apenas o dia (dd/mm)
        const labels = dados.map(d => {
          const dt = new Date(d.data_medicao);
          return dt.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' });
        });
        // Usa media_turbidez, trata nulo como zero
        const valores = dados.map(d => {
          const v = Number(d.media_turbidez);
          return isNaN(v) ? 0 : v;
        });
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
            title: { display: true, text: 'Data' }
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
