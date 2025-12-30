// assets/js/main.js
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser les tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
    
    // Initialiser les popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl)
    });
    
    // Confirmation avant suppression
    var deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer cet élément ?')) {
                e.preventDefault();
            }
        });
    });
    
    // Formater les nombres en monnaie XAF
    var moneyElements = document.querySelectorAll('.format-money');
    moneyElements.forEach(function(element) {
        var value = element.textContent.trim();
        if (!isNaN(value) && value !== '') {
            var formatted = parseInt(value).toLocaleString('fr-FR') + ' XAF';
            element.textContent = formatted;
        }
    });
    
    // Graphique des paiements
    var paymentChartCanvas = document.getElementById('paymentChart');
    if (paymentChartCanvas) {
        var ctx = paymentChartCanvas.getContext('2d');
        var paymentChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
                datasets: [{
                    label: 'Paiements (XAF)',
                    data: [12000000, 15000000, 14000000, 18000000, 20000000, 22000000, 
                           21000000, 19000000, 25000000, 25450000, 0, 0],
                    borderColor: '#009543',
                    backgroundColor: 'rgba(0, 149, 67, 0.1)',
                    borderWidth: 2,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('fr-FR').format(context.parsed.y) + ' XAF';
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('fr-FR').format(value) + ' XAF';
                            }
                        }
                    }
                }
            }
        });
    }
});