
$(function() {
    let $chart = $('#countDirsByStatus');
    
    if ($chart.length) {
        $chart.dataset = JSON.parse($chart.attr('data'));

        new Chart($chart, {
            type: 'pie',
            data: {
                labels: $chart.dataset.map(item => item.status.label),
                datasets: [{
                    label: $chart.data('label'),
                    data: $chart.dataset.map(item => item.count),
                    links: $chart.dataset.map(item => item.links.admin),
                    backgroundColor: $chart.dataset.map(item => item.color),
                    borderColor: $chart.dataset.map(item => item.color),
                    borderWidth: 1
                }]
            },
            options: {  
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: $chart.data('label'),
                        font: {
                            size: 14
                        }                    
                    },
                    legend: {
                        labels: {
                            generateLabels: (chart) => {
                                const datasets = chart.data.datasets;

                                return datasets[0].data.map((data, i) => ({
                                    text: `${chart.data.labels[i]} (${data})`,
                                    fillStyle: datasets[0].backgroundColor[i],
                                    strokeStyle: datasets[0].borderColor[i],
                                    hidden: !chart.getDataVisibility(i),
                                    index: i
                                }));
                            }
                        }
                    }                    
                },
                onClick: function(event, elements) {
                    if (!elements.length) return;
    
                    const element = elements[0];
    
                    window.location.href = this.data.datasets[element.datasetIndex].links[element.index];
    
                    return;
                }
            }
        });
    }
});