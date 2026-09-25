async function evolucion_detractores_promotores(sucursal) {
  try {
    fetch(`/reporteria/ajax/evolucion-detractores-promotores?sucursal=${sucursal}`)
      .then(res => res.json())
      .then(data => {
        // console.log(data);
        Highcharts.chart('grafico-promotores', {
          chart: { },
          title: { text: 'Evolución Detractores / Promotores (Anual)', align: 'left' },
          legend: { enabled: true },
          xAxis: { categories: data.categories },
          yAxis: {
            min: -60, max: 100, title: { text: null },
            gridLineDashStyle: 'ShortDot'
          },
          tooltip: { shared: true },
          plotOptions: {
            column: {
              stacking: 'normal',
              borderWidth: 0,
              groupPadding: 0.15,
              pointPadding: 0.05
            }
          },
          series: [
            { type: 'column', name: 'Promotores', color: '#02b3b1', data: data.promotores },
            { type: 'column', name: 'Detractores', color: '#f95f53', data: data.detractores },
            { type: 'line',   name: 'NPS', color: '#52cdff', data: data.nps, zIndex: 3,
              marker: { enabled: true, radius: 5, symbol: 'circle' }
            }
          ]
        });
      }
    );
  } catch (error) {
      console.error("Error cargando series:", error);
  }
}

async function distribucion_respuestas_nps(mes, sucursal) {
  html = '';
  try {
    fetch(`/reporteria/ajax/distribucion-respuestas-nps?mes=${mes}&sucursal=${sucursal}`)
      .then(res => res.json())
      .then(data => {
        // console.log(data.resultados);
        for(let i = 0; i < data.resultados.length; i++) {
          // console.log(data.resultados[i]);
          html += ` <tr>
                      <td class="py-2 ${data.resultados[i].valor <= 6 ? 'bg-danger text-white' : (data.resultados[i].valor > 8 ? 'bg-success text-white' : 'text-white fondo-amarillo')}">${data.resultados[i].valor}</td>
                      <td class="py-2">${data.resultados[i].cantidad}</td>
                      <td class="py-2">${data.resultados[i].porcentaje}%</td>
                    </tr>`;
        }
        $('#distribucion').html(html);
      }
    );
  } catch (error) {
      console.error("Error cargando series:", error);
  }
}

async function obtener_datos_generales(mes, sucursal) {
  try {
      const response = await fetch(`/reporteria/ajax/datos-generales?mes=${mes}&sucursal=${sucursal}`);
      if (!response.ok) throw new Error("Error en la petición");
      
      let datos = await response.json();

      $('#cantidad_respuestas').text(datos.total_encuestas_respondidas);
      $('#porcentaje_nps').text(datos.NPS);
      $('#cantidad_comentarios').text(datos.total_cantidad_comentarios);
      $('#porcentaje_detractores').text(datos.porcentaje_detractores+'%');
  } catch (error) {
      console.error("Error cargando datos:", error);
  }
}

async function evolucion_ibb_sucursales(sucursal) {
  try {
    fetch(`/reporteria/ajax/evolucion-ibb-sucursales?sucursal=${sucursal}`)
    .then(res => res.json())
    .then(data => {
      // 1. Extraer todos los periodos únicos ordenados
      const categorias = [...new Set(data.map(item => item.periodo))];

      // 2. Agrupar por sucursal
      const sucursales = {};
      data.forEach(item => {
        if (!sucursales[item.sucursal]) {
          sucursales[item.sucursal] = {};
        }
        sucursales[item.sucursal][item.periodo] = item.IBB;
      });

      // 3. Crear series (rellenando con null donde falte)
      const series = Object.keys(sucursales).map(suc => ({
        name: suc,
        data: categorias.map(periodo => sucursales[suc][periodo] ?? null)
      }));

      // 4. Renderizar Highcharts
      Highcharts.chart('evolutivo_sucursales', {
        chart: { type: 'line' },
        title: { text: 'Evolución NPS por Clínica (Anual)', align: 'left' },
        xAxis: { categories: categorias },
        yAxis: {
          title: { text: 'NPS' },
          min: -50, max: 100
        },
        tooltip: { shared: true },
        series: series
      });
    });
  } catch (error) {
      console.error("Error cargando series:", error);
  }
}


async function traer_comentarios(mes, sucursal, especialista = null) {
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

function actualizar_datos(mes, sucursal) {
  // traer_comentarios(mes, sucursal);
  distribucion_respuestas_nps(mes, sucursal);
  obtener_datos_generales(mes, sucursal);
  evolucion_detractores_promotores(sucursal);
  evolucion_ibb_sucursales(sucursal)
}













// Highcharts.addEvent(Highcharts.Point, 'click', function () {
//     if (this.series.options.className.indexOf('popup-on-click') !== -1) {
//         const chart = this.series.chart;
//         const date = chart.time.dateFormat('%A, %b %e, %Y', this.x);
//         const text = `<b>${date}</b><br/>${this.y} ${this.series.name}`;

//         const anchorX = this.plotX + this.series.xAxis.pos;
//         const anchorY = this.plotY + this.series.yAxis.pos;
//         const align = anchorX < chart.chartWidth - 200 ? 'left' : 'right';
//         const x = align === 'left' ? anchorX + 10 : anchorX - 10;
//         const y = anchorY - 30;
//         if (!chart.sticky) {
//             chart.sticky = chart.renderer
//                 .label(text, x, y, 'callout',  anchorX, anchorY)
//                 .attr({
//                     align,
//                     fill: 'rgba(0, 0, 0, 0.75)',
//                     padding: 10,
//                     zIndex: 7 // Above series, below tooltip
//                 })
//                 .css({
//                     color: 'white'
//                 })
//                 .on('click', function () {
//                     chart.sticky = chart.sticky.destroy();
//                 })
//                 .add();
//         } else {
//             chart.sticky
//                 .attr({ align, text })
//                 .animate({ anchorX, anchorY, x, y }, { duration: 250 });
//         }
//     }
// });




