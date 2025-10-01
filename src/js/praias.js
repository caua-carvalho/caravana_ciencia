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
  setInterval(buscarPraias, 5000);

  // Busca as praias do backend
  function buscarPraias() {
    fetch(API_PRAIAS)
      .then(res => res.json())
      .then(data => {
        praias = data;
        console.log("Praias carregadas:", praias);
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
  let pollingInterval = null;

  function abrirModalPraia(praia) {
    modalNome.textContent = praia.nome;
    modalDescricao.textContent = praia.descricao || '';
    modalFoto.src = praia.foto || 'https://imgmd.net/images/v1/guia/1611884/praia-vermelha-do-sul.jpg';
    modalTurbidez.textContent = `Turbidez: ${praia.turbidez_valor ?? 'N/A'}`;
    modalTurbidez.className = 'badge';

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

    // Abre o modal
    modal.show();

    // Atualiza gráfico imediatamente
    carregarGraficoPraia(praia.id);

    // Limpa intervalo anterior, se existir
    if (pollingInterval) clearInterval(pollingInterval);

    // Cria polling a cada 10s (ou outro valor que quiser)
    pollingInterval = setInterval(() => {
      carregarGraficoPraia(praia.id);
    }, 5000);

    // Quando modal fechar, limpa polling
    document.getElementById('modalPraia').addEventListener('hidden.bs.modal', () => {
      if (pollingInterval) clearInterval(pollingInterval);
    }, { once: true });
  }


  // Busca e mostra o gráfico de turbidez da última semana para a praia
function carregarGraficoPraia(idPraia) {
  const dataInicial = new Date();
  dataInicial.setDate(dataInicial.getDate() - 6);

  const dataInicialStr = `${dataInicial.getFullYear()}-${String(dataInicial.getMonth()+1).padStart(2,'0')}-${String(dataInicial.getDate()).padStart(2,'0')}`;

  fetch(API_HISTORICO, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id_praia: idPraia, data_inicial: dataInicialStr })
  })
    .then(res => res.json())
    .then(json => {
      if (json.status !== 'sucesso') throw new Error('Erro na API');
      const dados = json.dados.reverse();

      // Atualiza badge com o último valor
      const ultimoValor = dados.length ? Number(dados[dados.length - 1].media_turbidez) : null;
      atualizarBadgeTurbidez(ultimoValor);

      // Prepara gráfico
      const dias = [];
      for (let i = 0; i < 7; i++) {
        const d = new Date(dataInicial.getTime());
        d.setDate(d.getDate() + i);
        const str = `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
        dias.push(str);
      }

      const dadosMap = {};
      dados.forEach(d => {
        dadosMap[d.data_medicao] = Number(d.media_turbidez);
      });

      const labels = dias.map(dt => {
        const [y,m,d] = dt.split("-");
        return `${d}/${m}`;
      });
      const valores = dias.map(dt => dadosMap[dt] ?? 0);

      atualizarGrafico(labels, valores);
    })
    .catch(() => {
      atualizarGrafico([], []);
      atualizarBadgeTurbidez(null);
    });
}

// Função separada pra atualizar o badge
function atualizarBadgeTurbidez(valor) {
  modalTurbidez.textContent = `Turbidez: ${valor ?? 'N/A'}`;
  modalTurbidez.className = 'badge'; // reseta classes

  if (valor !== null && !isNaN(valor)) {
    if (valor <= 5) modalTurbidez.classList.add('bg-success');
    else if (valor <= 25) modalTurbidez.classList.add('bg-success','text-dark');
    else if (valor <= 50) modalTurbidez.classList.add('bg-warning','text-dark');
    else if (valor <= 100) modalTurbidez.classList.add('bg-warning');
    else if (valor <= 500) modalTurbidez.classList.add('bg-danger');
    else if (valor <= 1000) modalTurbidez.classList.add('bg-dark');
    else modalTurbidez.classList.add('bg-secondary');
  } else {
    modalTurbidez.classList.add('bg-secondary');
  }
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
        animation: false,
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
