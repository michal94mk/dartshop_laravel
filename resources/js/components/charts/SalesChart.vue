<template>
  <div class="w-full h-full">
    <canvas ref="chartCanvas"></canvas>
  </div>
</template>

<script>
import { ref, onMounted, watch, nextTick } from 'vue'
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  BarElement,
  Title,
  Tooltip,
  Legend,
  Filler,
  LineController,
  BarController
} from 'chart.js'

// Register Chart.js components
ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  BarElement,
  Title,
  Tooltip,
  Legend,
  Filler,
  LineController,
  BarController
)

export default {
  name: 'SalesChart',
  props: {
    data: {
      type: Array,
      default: () => []
    },
    chartType: {
      type: String,
      default: 'line'
    },
    metric: {
      type: String,
      default: 'revenue'
    }
  },
  setup(props) {
    const chartCanvas = ref(null)
    let chartInstance = null

    const createChart = () => {
      console.log('SalesChart createChart called with data:', props.data)
      console.log('Chart type:', props.chartType)
      console.log('Metric:', props.metric)
      console.log('Canvas element:', chartCanvas.value)
      
      if (!chartCanvas.value) {
        console.log('SalesChart: No canvas element found')
        return
      }
      
      if (!props.data.length) {
        console.log('SalesChart: No data provided')
        return
      }

      // Destroy existing chart
      if (chartInstance) {
        console.log('Destroying existing chart instance')
        chartInstance.destroy()
      }

      const ctx = chartCanvas.value.getContext('2d')
      
      // Prepare data based on metric
      const labels = props.data.map(item => {
        const date = new Date(item.date)
        return date.toLocaleDateString('pl-PL', { month: 'short', day: 'numeric' })
      })
      
      let dataValues = []
      let label = ''
      let borderColor = ''
      let backgroundColor = ''
      
      switch (props.metric) {
        case 'revenue':
          dataValues = props.data.map(item => item.total_sales)
          label = 'Przychód (PLN)'
          borderColor = 'rgb(99, 102, 241)'
          backgroundColor = 'rgba(99, 102, 241, 0.1)'
          break
        case 'orders':
          dataValues = props.data.map(item => item.order_count)
          label = 'Liczba zamówień'
          borderColor = 'rgb(34, 197, 94)'
          backgroundColor = 'rgba(34, 197, 94, 0.1)'
          break
        case 'average':
          dataValues = props.data.map(item => 
            item.order_count > 0 ? (item.total_sales / item.order_count) : 0
          )
          label = 'Średnia wartość zamówienia (PLN)'
          borderColor = 'rgb(168, 85, 247)'
          backgroundColor = 'rgba(168, 85, 247, 0.1)'
          break
      }

      const chartConfig = {
        type: props.chartType === 'area' ? 'line' : props.chartType,
        data: {
          labels: labels,
          datasets: [{
            label: label,
            data: dataValues,
            borderColor: borderColor,
            backgroundColor: backgroundColor,
            borderWidth: 2,
            fill: props.chartType === 'area',
            tension: props.chartType === 'line' || props.chartType === 'area' ? 0.4 : 0,
            pointBackgroundColor: borderColor,
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: true,
              position: 'top',
            },
            tooltip: {
              mode: 'index',
              intersect: false,
              backgroundColor: 'rgba(0, 0, 0, 0.8)',
              titleColor: '#fff',
              bodyColor: '#fff',
              borderColor: borderColor,
              borderWidth: 1,
              callbacks: {
                label: function(context) {
                  let value = context.parsed.y
                  if (props.metric === 'revenue' || props.metric === 'average') {
                    return `${context.dataset.label}: ${value.toFixed(2)} PLN`
                  }
                  return `${context.dataset.label}: ${value}`
                }
              }
            }
          },
          scales: {
            x: {
              display: true,
              grid: {
                display: false
              },
              ticks: {
                color: '#6B7280'
              }
            },
            y: {
              display: true,
              grid: {
                color: 'rgba(0, 0, 0, 0.1)'
              },
              ticks: {
                color: '#6B7280',
                callback: function(value) {
                  if (props.metric === 'revenue' || props.metric === 'average') {
                    return value.toFixed(0) + ' PLN'
                  }
                  return value
                }
              }
            }
          },
          interaction: {
            mode: 'nearest',
            axis: 'x',
            intersect: false
          }
        }
      }

      chartInstance = new ChartJS(ctx, chartConfig)
      console.log('SalesChart: Chart created successfully:', chartInstance)
    }

    // Watch for changes in props
    watch([() => props.data, () => props.chartType, () => props.metric], () => {
      nextTick(() => {
        createChart()
      })
    }, { deep: true })

    onMounted(() => {
      nextTick(() => {
        createChart()
      })
    })

    return {
      chartCanvas
    }
  }
}
</script>

<style scoped>
canvas {
  max-height: 100%;
}
</style> 