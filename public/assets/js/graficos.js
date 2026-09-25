function generar_grafico(series, id_sucursal, sucursal) {
  console.log(series);
  Highcharts.chart(`grafico-${id_sucursal}`, {
  chart: {
    type: 'column',
  },
  title: {
    text: sucursal
  },
  xAxis: {
    categories: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
    crosshair: true
  },
  yAxis: {
    min: 0,
    title: { text: 'Cantidad de respuestas' }
  },
  legend: { enabled: false },
  tooltip: {
    shared: true,
    formatter: function () {
      let total = 0;
      this.points.forEach(p => total += p.y);
      let tooltip = ``;
      this.points.forEach(p => {
        let porcentaje = ((p.y / total) * 100).toFixed(1);
        tooltip += `${p.series.name}: <b>${p.y}</b> (${porcentaje}%)<br/>`;
      });
      tooltip += `<b>Total Respondidos: ${total}</b>`;
      return tooltip;
    }
  },
  plotOptions: { column: { stacking: 'normal', dataLabels: { enabled: false } } },
  legend: {
        align: 'center',
        verticalAlign: 'top',
        layout: 'horizontal',
        itemStyle: {
            fontSize: '14px'
        },
        itemHoverStyle: {
            fontSize: '14px'
        }
  },
  series: series
  // series: [
  //   { name: 'Promotores', data: [120,150,180,160,140,170,200], color: '#23f788' },
  //   { name: 'Neutros', data: [30,40,20,50,60,30,25], color: '#f5ca23' },
  //   { name: 'Detractores', data: [10,20,15,10,25,20,15], color: '#f52324' }
  // ]
});
}












// Highcharts.chart('grafico-2', {
//       chart: {
//         type: 'column'
//       },
//       title: {
//         text: 'Burgos'
//       },
//       xAxis: {
//         categories: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio'],
//         crosshair: true
//       },
//       yAxis: {
//         min: 0,
//         title: {
//           text: 'Cantidad de respuestas'
//         }
//       },
//       legend: {
//         enabled: false // 🚫 quita la leyenda superior
//       },
//       tooltip: {
//         shared: true,
//         formatter: function () {
//           let total = 0;
//           this.points.forEach(p => total += p.y);

//           let tooltip = ``;
//           this.points.forEach(p => {
//             let porcentaje = ((p.y / total) * 100).toFixed(1);
//             tooltip += `${p.series.name}: <b>${p.y}</b> (${porcentaje}%)<br/>`;
//           });
//           tooltip += `<b>Total Respondidos: ${total}</b>`;
//           return tooltip;
//         }
//       },
//       plotOptions: {
//         column: {
//           stacking: 'normal',
//           dataLabels: {
//             enabled: false // 🚫 no muestra números dentro de la barra
//           }
//         }
//       },
//       legend: {
//         align: 'center',
//         verticalAlign: 'top',
//         layout: 'horizontal',
//         itemStyle: {
//             fontSize: '14px'
//         },
//         itemHoverStyle: {
//             fontSize: '14px'
//         }
//   },
//       series: [
//         {
//           name: 'Promotores',
//           data: [130, 180, 100, 100, 170, 110, 100],
//           color: '#23f788'
//         },
//         {
//           name: 'Neutros',
//           data: [10, 50, 30, 20, 40, 30, 25],
//           color: '#f5ca23'
//         },
//         {
//           name: 'Detractores',
//           data: [20, 10, 15, 20, 20, 22, 16],
//           color: '#f52324'
//         }
//       ]
//     });


// Highcharts.chart('grafico-3', {
//       chart: {
//         type: 'column'
//       },
//       title: {
//         text: 'Tenderini'
//       },
//       xAxis: {
//         categories: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio'],
//         crosshair: true
//       },
//       yAxis: {
//         min: 0,
//         title: {
//           text: 'Cantidad de respuestas'
//         }
//       },
//       legend: {
//         enabled: false // 🚫 quita la leyenda superior
//       },
//       tooltip: {
//         shared: true,
//         formatter: function () {
//           let total = 0;
//           this.points.forEach(p => total += p.y);

//           let tooltip = ``;
//           this.points.forEach(p => {
//             let porcentaje = ((p.y / total) * 100).toFixed(1);
//             tooltip += `${p.series.name}: <b>${p.y}</b> (${porcentaje}%)<br/>`;
//           });
//           tooltip += `<b>Total Respondidos: ${total}</b>`;
//           return tooltip;
//         }
//       },
//       plotOptions: {
//         column: {
//           stacking: 'normal',
//           dataLabels: {
//             enabled: false // 🚫 no muestra números dentro de la barra
//           }
//         }
//       },
//       legend: {
//         align: 'center',
//         verticalAlign: 'top',
//         layout: 'horizontal',
//         itemStyle: {
//             fontSize: '14px'
//         },
//         itemHoverStyle: {
//             fontSize: '14px'
//         }
//   },
//       series: [
//         {
//           name: 'Promotores',
//           data: [120, 150, 180, 160, 140, 170, 200],
//           color: '#23f788'
//         },
//         {
//           name: 'Neutros',
//           data: [30, 40, 20, 50, 60, 30, 25],
//           color: '#f5ca23'
//         },
//         {
//           name: 'Detractores',
//           data: [10, 20, 15, 10, 25, 20, 15],
//           color: '#f52324'
//         }
//       ]
//     });


// Highcharts.chart('grafico-4', {
//       chart: {
//         type: 'column'
//       },
//       title: {
//         text: 'Alameda'
//       },
//       xAxis: {
//         categories: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio'],
//         crosshair: true
//       },
//       yAxis: {
//         min: 0,
//         title: {
//           text: 'Cantidad de respuestas'
//         }
//       },
//       legend: {
//         enabled: false // 🚫 quita la leyenda superior
//       },
//       tooltip: {
//         shared: true,
//         formatter: function () {
//           let total = 0;
//           this.points.forEach(p => total += p.y);

//           let tooltip = ``;
//           this.points.forEach(p => {
//             let porcentaje = ((p.y / total) * 100).toFixed(1);
//             tooltip += `${p.series.name}: <b>${p.y}</b> (${porcentaje}%)<br/>`;
//           });
//           tooltip += `<b>Total Respondidos: ${total}</b>`;
//           return tooltip;
//         }
//       },
//       plotOptions: {
//         column: {
//           stacking: 'normal',
//           dataLabels: {
//             enabled: false // 🚫 no muestra números dentro de la barra
//           }
//         }
//       },
//       legend: {
//         align: 'center',
//         verticalAlign: 'top',
//         layout: 'horizontal',
//         itemStyle: {
//             fontSize: '14px'
//         },
//         itemHoverStyle: {
//             fontSize: '14px'
//         }
//   },
//       series: [
//         {
//           name: 'Promotores',
//           data: [30, 40, 55, 20, 25, 30, 45],
//           color: '#23f788'
//         },
//         {
//           name: 'Neutros',
//           data: [30, 40, 20, 50, 60, 30, 25],
//           color: '#f5ca23'
//         },
//         {
//           name: 'Detractores',
//           data: [10, 15, 18, 16, 14, 17, 20],
//           color: '#f52324'
//         }
//       ]
//     });


// Highcharts.chart('grafico-5', {
//       chart: {
//         type: 'column'
//       },
//       title: {
//         text: 'Providencia'
//       },
//       xAxis: {
//         categories: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio'],
//         crosshair: true
//       },
//       yAxis: {
//         min: 0,
//         title: {
//           text: 'Cantidad de respuestas'
//         }
//       },
//       legend: {
//         enabled: false // 🚫 quita la leyenda superior
//       },
//       tooltip: {
//         shared: true,
//         formatter: function () {
//           let total = 0;
//           this.points.forEach(p => total += p.y);

//           let tooltip = ``;
//           this.points.forEach(p => {
//             let porcentaje = ((p.y / total) * 100).toFixed(1);
//             tooltip += `${p.series.name}: <b>${p.y}</b> (${porcentaje}%)<br/>`;
//           });
//           tooltip += `<b>Total Respondidos: ${total}</b>`;
//           return tooltip;
//         }
//       },
//       plotOptions: {
//         column: {
//           stacking: 'normal',
//           dataLabels: {
//             enabled: false // 🚫 no muestra números dentro de la barra
//           }
//         }
//       },
//       legend: {
//         align: 'center',
//         verticalAlign: 'top',
//         layout: 'horizontal',
//         itemStyle: {
//             fontSize: '14px'
//         },
//         itemHoverStyle: {
//             fontSize: '14px'
//         }
//   },
//       series: [
//         {
//           name: 'Promotores',
//           data: [100, 180, 120, 100, 140, 110, 160],
//           color: '#23f788'
//         },
//         {
//           name: 'Neutros',
//           data: [70, 45, 30, 54, 67, 40, 75],
//           color: '#f5ca23'
//         },
//         {
//           name: 'Detractores',
//           data: [20, 10, 65, 70, 25, 70, 25],
//           color: '#f52324'
//         }
//       ]
//     });