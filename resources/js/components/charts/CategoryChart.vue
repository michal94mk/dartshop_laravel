<template>
  <div class="w-full h-full">
    <canvas ref="chartCanvas"></canvas>
  </div>
</template>

<script>
import { ref, onMounted, watch, nextTick } from 'vue'
import {
  Chart as ChartJS,
  ArcElement,
  Tooltip,
  Legend,
  PieController
} from 'chart.js'

// Register Chart.js components
ChartJS.register(
  ArcElement,
  Tooltip,
  Legend,
  PieController
)

export default {
  name: 'CategoryChart',
  props: {
    data: {
      type: Array,
      default: () => []
    }
  },
  setup(props) {
    const chartCanvas = ref(null)
    let chartInstance = null

    const createChart = () => {
      console.log('CategoryChart createChart called with data:', props.data)
      console.log('Canvas element:', chartCanvas.value)
      
      if (!chartCanvas.value) {
        console.log('CategoryChart: No canvas element found')
        return
      }
      
      if (!props.data.length) {
        console.log('CategoryChart: No data provided')
        return
      }

      // Destroy existing chart
      if (chartInstance) {
        console.log('Destroying existing chart instance')
        chartInstance.destroy()
      }

      const ctx = chartCanvas.value.getContext('2d')
      
      // Prepare data
      const labels = props.data.map(item => item.name)
      const dataValues = props.data.map(item => item.products_count)
      
      // Generate colors
      const colors = [
        'rgba(99, 102, 241, 0.8)',
        'rgba(34, 197, 94, 0.8)',
        'rgba(168, 85, 247, 0.8)',
        'rgba(251, 191, 36, 0.8)',
        'rgba(239, 68, 68, 0.8)',
        'rgba(59, 130, 246, 0.8)',
        'rgba(16, 185, 129, 0.8)',
        'rgba(245, 101, 101, 0.8)'
      ]

      const chartConfig = {
        type: 'pie',
        data: {
          labels: labels,
          datasets: [{
            data: dataValues,
            backgroundColor: colors.slice(0, dataValues.length),
            borderColor: colors.slice(0, dataValues.length).map(color => color.replace('0.8', '1')),
            borderWidth: 2,
            hoverOffset: 4
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: true,
              position: 'right',
              labels: {
                padding: 20,
                usePointStyle: true,
                font: {
                  size: 12
                }
              }
            },
            tooltip: {
              backgroundColor: 'rgba(0, 0, 0, 0.8)',
              titleColor: '#fff',
              bodyColor: '#fff',
              borderWidth: 1,
              callbacks: {
                label: function(context) {
                  const total = context.dataset.data.reduce((a, b) => a + b, 0)
                  const percentage = ((context.parsed / total) * 100).toFixed(1)
                  return `${context.label}: ${context.parsed} (${percentage}%)`
                }
              }
            }
          }
        }
      }

      chartInstance = new ChartJS(ctx, chartConfig)
      console.log('CategoryChart: Chart created successfully:', chartInstance)
    }

    // Watch for changes in props
    watch(() => props.data, () => {
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