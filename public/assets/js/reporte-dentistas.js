async function evolucion_ibb_sucursales(sucursal) {
    try {
        const response = await fetch(`/reporteria/ajax/ibb-dentistas?sucursal=${sucursal}`);
        const data = await response.json();
        
        const tbody = document.getElementById('dentistas');
        tbody.innerHTML = '';
        
        const formatNum = (num) => {
            if (num === null || num === undefined) return '-';
            return parseFloat(num).toFixed(1);
        };
        
        const getIBBClass = (ibb) => {
            const valor = parseFloat(ibb);
            if (valor >= 50) return 'text-success fw-bold';
            if (valor >= 0) return 'text-warning fw-bold';
            return 'text-danger fw-bold';
        };
        
        const getLikertClass = (valor) => {
            const num = parseFloat(valor);
            if (isNaN(num)) return '';
            
            if (num >= 4.5) return 'bg-success text-white fw-semibold';
            if (num >= 4.0) return 'bg-success bg-opacity-75 fw-semibold';
            if (num >= 3.5) return 'bg-success bg-opacity-50';
            if (num >= 3.0) return 'bg-warning bg-opacity-50';
            if (num >= 2.5) return 'bg-warning bg-opacity-75';
            if (num >= 2.0) return 'bg-danger bg-opacity-50';
            return 'bg-danger text-white fw-semibold';
        };
        
        // Agrupar por centro
        let centroActual = '';
        
        data.forEach((dentista, index) => {
            // Agregar separador de centro
            if (dentista.centro !== centroActual) {
                centroActual = dentista.centro;
                const separador = document.createElement('tr');
                separador.className = 'table-secondary';
                separador.innerHTML = `
                    <td colspan="10" class="fw-bold py-2">
                        <i class="bi bi-building me-2"></i>${dentista.centro}
                    </td>
                `;
                tbody.appendChild(separador);
            }
            
            const row = document.createElement('tr');
            row.className = index % 2 === 0 ? '' : 'table-light';
            
            row.innerHTML = `
                <td class="text-muted">${dentista.id_dentista}</td>
                <td class="fw-medium">${dentista.doctor}</td>
                <td class="text-muted small">${dentista.centro}</td>
                <td class="text-muted small">${dentista.especialidad}</td>
                <td class="text-center">
                    <span class="badge bg-primary">${dentista.cantidad_respuesta}</span>
                </td>
                <td class="text-center">
                    <span class="badge bg-info" style="cursor: pointer;" onclick="traer_comentarios_especialista(null, ${dentista.id_sucursal}, ${dentista.id_dentista});">${dentista.cantidad_comentarios}</span>
                </td>
                <td class="text-center ${getLikertClass(dentista.satisfaccion_tiempo_espera)}" style="font-weight: 500;">
                    ${formatNum(dentista.satisfaccion_tiempo_espera)}
                </td>
                <td class="text-center ${getLikertClass(dentista.claridad_explicacion)}" style="font-weight: 500;">
                    ${formatNum(dentista.claridad_explicacion)}
                </td>
                <td class="text-center ${getLikertClass(dentista.delicadeza_procedimiento)}" style="font-weight: 500;">
                    ${formatNum(dentista.delicadeza_procedimiento)}
                </td>
                <td class="text-center ${getIBBClass(dentista.IBB)}" style="font-size: 0.95rem;">
                    ${formatNum(dentista.IBB)}%
                </td>
            `;
            
            tbody.appendChild(row);
        });
        
    } catch (error) {
        console.error("Error cargando datos:", error);
        const tbody = document.getElementById('dentistas');
        tbody.innerHTML = `
            <tr>
                <td colspan="10" class="text-center text-danger py-4">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Error al cargar los datos
                </td>
            </tr>
        `;
    }
}


async function traer_comentarios_especialista(mes, sucursal, especialista) {
  $('#modal_comentario_periodo').modal("show");
  html = '';
  try {
    fetch(`/reporteria/ajax/traer-comentarios?mes=${mes}&sucursal=${sucursal}&especialista=${especialista}`)
      .then(res => res.json())
      .then(data => {
        // console.log(data);
        for(let i = 0; i < data.length; i++) {
          // console.log(data[i]);
          html += ` <tr>
                      <td style="white-space: normal !important;word-wrap: break-word;word-break: break-word;white-space: normal !important;word-break: break-word;" class="py-2">${data[i].comentario}</td>
                      <td class="py-2">${data[i].nombre_dentista}</td>
                      <td class="py-2">${data[i].sucursal}</td>
                      <td class="py-2">${data[i].fecha_envio}</td>
                    </tr>`;
        }
        $('#tbody_comentarios_periodo').html(html);
      }
    );
  } catch (error) {
      console.error("Error cargando series:", error);
  }
}

evolucion_ibb_sucursales($('#select_sucursal').val());