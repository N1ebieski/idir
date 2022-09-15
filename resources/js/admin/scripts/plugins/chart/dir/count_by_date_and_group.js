
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

$(document).on('ready.n1ebieski/idir/admin/scripts/plugins/chart/dir@countByDateAndGroup', function () {
    let $chart = $('#count-dirs-by-date-and-group');

    if ($chart.length) {
        $chart.dataset = JSON.parse($chart.attr('data'));

        let timeline = [...new Map($chart.dataset.map(item => [`${item.month}.${item.year}`, item])).values()];
        let groups = [...new Map($chart.dataset.map(item => [item.group.id, item])).values()];
        let sum = 0;

        $chart.chart({
            type: 'bar',            
            data: {
                datasets: [{
                    label: $chart.data('all-label'),
                    type: 'line',
                    backgroundColor: 'rgb(0, 123, 255)',
                    borderColor: 'rgb(0, 123, 255)',
                    borderWidth: 1,                   
                    data: timeline.map(t => {
                        return {
                            x: `${t.month}.${t.year}`,
                            y: $chart.dataset.filter(i => i.month === t.month && i.year === t.year)
                                .reduce((prev, i) => { return sum = prev + i.count }, sum)
                        };
                    })
                }].concat(groups.map(item => {
                    return {
                        label: item.group.name,
                        data: timeline.map(t => {
                            return $chart.dataset.find(i => {
                                return i.month === t.month && i.year === t.year && i.group.id === item.group.id;
                            })?.count || 0;
                        }),
                        backgroundColor: item.color,
                        borderColor: item.color,
                        borderWidth: 1               
                    };
                }))
            },
            options: {  
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        stacked: true,
                        title: {
                            color: $chart.data('font-color') || "#666",
                            display: true,
                            text: $chart.data('x-label')
                        },
                        ticks: {
                            color: $chart.data('font-color') || "#666"
                        }                     
                    },
                    y: {
                        stacked: true,
                        title: {
                            color: $chart.data('font-color') || "#666",
                            display: true,
                            text: $chart.data('y-label')
                        },
                        ticks: {
                            color: $chart.data('font-color') || "#666"
                        }                    
                    }
                },                
                plugins: {
                    legend: {
                        labels: {
                            color: $chart.data('font-color') || "#666"
                        }
                    },                    
                    title: {
                        display: true,
                        text: $chart.data('label'),
                        color: $chart.data('font-color') || "#666",                        
                        font: {
                            size: 14
                        }                    
                    }              
                }
            }
        });

        if (timeline.length > 15) {
            let width = timeline.length * 50;

            $chart.parent().css('width', width);
            $chart.parents().eq(1).scrollLeft(width);
        }
    }
});