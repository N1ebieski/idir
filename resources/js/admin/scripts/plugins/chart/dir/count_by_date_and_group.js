
$(function() {
    let $chart = $('#countDirsByDateAndGroup');

    if ($chart.length) {
        $chart.dataset = JSON.parse($chart.attr('data'));

        let timeline = [...new Map($chart.dataset.map(item => [`${item.month}.${item.year}`, item])).values()];
        let groups = [...new Map($chart.dataset.map(item => [item.group.id, item])).values()];

        new Chart($chart, {
            type: 'bar',            
            data: {
                datasets: [{
                    type: 'line',
                    backgroundColor: 'rgb(0, 123, 255)',
                    borderColor: 'rgb(0, 123, 255)',
                    borderWidth: 1,                    
                    data: timeline.map(item => {
                        return {
                            x: `${item.month}.${item.year}`,
                            y: $chart.dataset.filter(i => i.month === item.month && i.year === item.year)
                                .reduce((sum, i) => { return sum + i.count }, 0)
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
                            display: true,
                            text: 'Date'
                        }
                    },
                    y: {
                        stacked: true,
                        title: {
                            display: true,
                            text: 'value'
                        }
                    }
                },                
                plugins: {
                    title: {
                        display: true,
                        text: $chart.data('label'),
                        font: {
                            size: 14
                        }                    
                    }
                    // legend: {
                    //     labels: {
                    //         generateLabels: (chart) => {
                    //             const datasets = chart.data.datasets;

                    //             return datasets[0].data.map((data, i) => ({
                    //                 text: `${chart.data.labels[i]} (${data})`,
                    //                 fillStyle: datasets[0].backgroundColor[i],
                    //                 strokeStyle: datasets[0].borderColor[i],
                    //                 hidden: !chart.getDataVisibility(i),
                    //                 index: i
                    //             }));
                    //         }
                    //     }
                    // }                    
                }
                // onClick: function(event, elements) {
                //     if (!elements.length) return;
    
                //     const element = elements[0];
    
                //     window.location.href = this.data.datasets[element.datasetIndex].url[element.index];
    
                //     return;
                // }
            }
        });

        let width = timeline.length * 50;

        $chart.parent().css('width', width);
        $chart.parents().eq(1).scrollLeft(width);
    }
});