import { Controller } from '@hotwired/stimulus'
import Chart from 'chart.js/auto'

export default class extends Controller {
    static values = {
        chartData: Object,
        chartTitle: String,
        chartType: String,
    }
    connect() {
        new Chart(
            this.element,
            {
                type: this.chartTypeValue,
                data: {
                    labels: this.chartDataValue.xAxis,
                    datasets: this.chartDataValue.datasets,
                },
                options: {
                    plugins: {
                        title: {
                            display: true,
                            text: this.chartTitleValue
                        }
                    }
                }
            }
        )
    }
}
