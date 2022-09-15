/*
 * NOTICE OF LICENSE
 * 
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 * 
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 * 
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

$(document).on('ready.n1ebieski/idir/admin/scripts/plugins/bootstrap-confirmation/dir@countByGroup', function () {
    let $chart = $('#count-dirs-by-group');
    
    if ($chart.length) {
        $chart.dataset = JSON.parse($chart.attr('data'));

        $chart.chart({
            type: 'pie',
            data: {
                labels: $chart.dataset.map(item => item.group.name),
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
                        color: $chart.data('font-color') || "#666",
                        font: {
                            size: 14
                        }                    
                    },
                    legend: {
                        labels: {
                            color: $chart.data('font-color') || "#666",
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
                onClick: function (event, elements) {
                    if (!elements.length) return;
    
                    const element = elements[0];
    
                    window.location.href = this.data.datasets[element.datasetIndex].links[element.index];
    
                    return;
                }
            }
        });
    }
});