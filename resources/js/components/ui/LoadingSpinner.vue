<template>
  <div :class="containerClass">
    <!-- Full Screen App Loading (for app initialization) -->
    <div v-if="fullScreen" class="app-loading-overlay">
      <div class="app-loading-content">
        <!-- Animated Logo/Brand -->
        <div class="brand-logo">
          <div class="logo-ring ring-1"></div>
          <div class="logo-ring ring-2"></div>
          <div class="logo-ring ring-3"></div>
          <div class="logo-center">
            <div class="logo-icon">
              <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M7 4V2C7 1.45 7.45 1 8 1H16C16.55 1 17 1.45 17 2V4H20C20.55 4 21 4.45 21 5S20.55 6 20 6H19V19C19 20.1 18.1 21 17 21H7C5.9 21 5 20.1 5 19V6H4C3.45 6 3 5.55 3 5S3.45 4 4 4H7ZM9 3V4H15V3H9ZM7 6V19H17V6H7Z"/>
              </svg>
            </div>
          </div>
        </div>
        
        <!-- App Loading Progress -->
        <div class="app-progress">
          <div class="progress-track">
            <div class="progress-bar"></div>
          </div>
          <div class="app-loading-text">{{ message || 'Inicjalizacja DartShop...' }}</div>
        </div>
        
        <!-- Floating Elements -->
        <div class="floating-elements">
          <div class="element element-1"></div>
          <div class="element element-2"></div>
          <div class="element element-3"></div>
          <div class="element element-4"></div>
        </div>
      </div>
    </div>
    
    <!-- Regular Component Loading -->
    <div v-else class="loading-spinner-wrapper">
      <!-- Main Spinner with Gradient -->
      <div class="relative">
        <!-- Outer Ring -->
        <div class="spinner-ring">
          <div class="spinner-segment segment-1"></div>
          <div class="spinner-segment segment-2"></div>
          <div class="spinner-segment segment-3"></div>
          <div class="spinner-segment segment-4"></div>
        </div>
        
        <!-- Inner Pulse -->
        <div class="spinner-pulse"></div>
        
        <!-- Center Dot -->
        <div class="spinner-center"></div>
      </div>
      
      <!-- Floating Dots -->
      <div class="floating-dots">
        <div class="dot dot-1"></div>
        <div class="dot dot-2"></div>
        <div class="dot dot-3"></div>
      </div>
    </div>
    
    <p v-if="message && !fullScreen" class="loading-text">{{ message }}</p>
  </div>
</template>

<script>
export default {
  name: 'LoadingSpinner',
  props: {
    message: {
      type: String,
      default: 'Ładowanie...'
    },
    fullScreen: {
      type: Boolean,
      default: false
    }
  },
  computed: {
    containerClass() {
      if (this.fullScreen) {
        return 'full-screen-container'
      }
      return 'loading-spinner-container'
    }
  }
}
</script>

<style scoped>
/* Full Screen App Loading */
.full-screen-container {
  position: fixed;
  inset: 0;
  z-index: 9999;
  background: linear-gradient(135deg, 
    rgba(79, 70, 229, 0.1) 0%, 
    rgba(124, 58, 237, 0.1) 50%, 
    rgba(236, 72, 153, 0.1) 100%
  );
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
}

.app-loading-overlay {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  background: radial-gradient(circle at center, 
    rgba(255, 255, 255, 0.1) 0%, 
    rgba(255, 255, 255, 0.05) 100%
  );
}

.app-loading-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 3rem;
  text-align: center;
}

/* Animated Brand Logo */
.brand-logo {
  position: relative;
  width: 120px;
  height: 120px;
}

.logo-ring {
  position: absolute;
  border-radius: 50%;
  border: 2px solid transparent;
  border-top: 2px solid;
  border-right: 2px solid;
}

.ring-1 {
  inset: 0;
  border-image: linear-gradient(45deg, #4f46e5, #7c3aed) 1;
  animation: logoRotate 3s linear infinite;
}

.ring-2 {
  inset: 15px;
  border-image: linear-gradient(135deg, #7c3aed, #ec4899) 1;
  animation: logoRotate 2s linear infinite reverse;
}

.ring-3 {
  inset: 30px;
  border-image: linear-gradient(225deg, #ec4899, #f59e0b) 1;
  animation: logoRotate 4s linear infinite;
}

.logo-center {
  position: absolute;
  inset: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #4f46e5, #7c3aed);
  border-radius: 50%;
  box-shadow: 0 0 40px rgba(79, 70, 229, 0.3);
  animation: logoPulse 2s ease-in-out infinite;
}

.logo-icon {
  width: 24px;
  height: 24px;
  color: white;
  animation: iconFloat 3s ease-in-out infinite;
}

/* App Progress */
.app-progress {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1.5rem;
  min-width: 300px;
}

.progress-track {
  width: 100%;
  height: 4px;
  background: rgba(79, 70, 229, 0.1);
  border-radius: 2px;
  overflow: hidden;
  backdrop-filter: blur(10px);
}

.progress-bar {
  height: 100%;
  width: 0%;
  background: linear-gradient(90deg, #4f46e5, #7c3aed, #ec4899, #f59e0b);
  border-radius: 2px;
  animation: progressLoad 2s ease-in-out infinite;
  box-shadow: 0 0 20px rgba(79, 70, 229, 0.4);
}

.app-loading-text {
  font-size: 1.125rem;
  font-weight: 600;
  background: linear-gradient(45deg, #4f46e5, #7c3aed, #ec4899);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  animation: textGlow 2s ease-in-out infinite;
  text-shadow: 0 0 20px rgba(79, 70, 229, 0.2);
}

/* Floating Elements */
.floating-elements {
  position: absolute;
  inset: -100px;
  pointer-events: none;
}

.element {
  position: absolute;
  width: 8px;
  height: 8px;
  background: linear-gradient(45deg, #4f46e5, #7c3aed);
  border-radius: 50%;
  opacity: 0.6;
  box-shadow: 0 0 15px rgba(79, 70, 229, 0.4);
}

.element-1 {
  top: 20%;
  left: 10%;
  animation: floatElement 6s ease-in-out infinite;
}

.element-2 {
  top: 10%;
  right: 20%;
  animation: floatElement 4s ease-in-out infinite 1s;
}

.element-3 {
  bottom: 20%;
  left: 20%;
  animation: floatElement 5s ease-in-out infinite 2s;
}

.element-4 {
  bottom: 10%;
  right: 10%;
  animation: floatElement 7s ease-in-out infinite 3s;
}

/* Regular Component Loading (existing styles) */
.loading-spinner-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 300px;
  position: relative;
}

.loading-spinner-wrapper {
  position: relative;
  width: 80px;
  height: 80px;
}

/* Main Spinner Ring */
.spinner-ring {
  position: absolute;
  inset: 0;
  border-radius: 50%;
  animation: rotate 2s linear infinite;
}

.spinner-segment {
  position: absolute;
  inset: 2px;
  border-radius: 50%;
  border: 3px solid transparent;
}

.segment-1 {
  border-top: 3px solid;
  border-image: linear-gradient(45deg, #4f46e5, #7c3aed) 1;
  animation: spin 1.5s ease-in-out infinite;
}

.segment-2 {
  border-right: 3px solid;
  border-image: linear-gradient(135deg, #7c3aed, #ec4899) 1;
  animation: spin 1.5s ease-in-out infinite 0.2s;
}

.segment-3 {
  border-bottom: 3px solid;
  border-image: linear-gradient(225deg, #ec4899, #f59e0b) 1;
  animation: spin 1.5s ease-in-out infinite 0.4s;
}

.segment-4 {
  border-left: 3px solid;
  border-image: linear-gradient(315deg, #f59e0b, #4f46e5) 1;
  animation: spin 1.5s ease-in-out infinite 0.6s;
}

/* Inner Pulse */
.spinner-pulse {
  position: absolute;
  top: 50%;
  left: 50%;
  width: 40px;
  height: 40px;
  margin: -20px 0 0 -20px;
  border-radius: 50%;
  background: linear-gradient(45deg, 
    rgba(79, 70, 229, 0.3), 
    rgba(124, 58, 237, 0.3), 
    rgba(236, 72, 153, 0.3)
  );
  animation: pulse 2s ease-in-out infinite;
}

/* Center Dot */
.spinner-center {
  position: absolute;
  top: 50%;
  left: 50%;
  width: 12px;
  height: 12px;
  margin: -6px 0 0 -6px;
  border-radius: 50%;
  background: linear-gradient(135deg, #4f46e5, #7c3aed);
  box-shadow: 0 0 20px rgba(79, 70, 229, 0.6);
  animation: centerPulse 1s ease-in-out infinite alternate;
}

/* Floating Dots */
.floating-dots {
  position: absolute;
  inset: -20px;
}

.dot {
  position: absolute;
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: linear-gradient(45deg, #4f46e5, #7c3aed);
  box-shadow: 0 0 10px rgba(79, 70, 229, 0.4);
}

.dot-1 {
  top: 10px;
  left: 50%;
  margin-left: -3px;
  animation: float 3s ease-in-out infinite;
}

.dot-2 {
  top: 50%;
  right: 10px;
  margin-top: -3px;
  animation: float 3s ease-in-out infinite 1s;
}

.dot-3 {
  bottom: 10px;
  left: 50%;
  margin-left: -3px;
  animation: float 3s ease-in-out infinite 2s;
}

/* Loading Text */
.loading-text {
  margin-top: 2rem;
  font-size: 1.1rem;
  font-weight: 600;
  background: linear-gradient(45deg, #4f46e5, #7c3aed, #ec4899);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  text-align: center;
  animation: textShimmer 2s ease-in-out infinite;
}

/* Full Screen Animations */
@keyframes logoRotate {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

@keyframes logoPulse {
  0%, 100% { 
    transform: scale(1);
    box-shadow: 0 0 40px rgba(79, 70, 229, 0.3);
  }
  50% { 
    transform: scale(1.08);
    box-shadow: 0 0 70px rgba(79, 70, 229, 0.6);
  }
}

@keyframes iconFloat {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-4px); }
}

@keyframes progressLoad {
  0% { width: 0%; }
  50% { width: 85%; }
  100% { width: 100%; }
}

@keyframes textGlow {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.8; }
}

@keyframes floatElement {
  0%, 100% { 
    transform: translateY(0px) scale(1);
    opacity: 0.6;
  }
  50% { 
    transform: translateY(-20px) scale(1.1);
    opacity: 1;
  }
}

/* Regular Component Animations */
@keyframes rotate {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

@keyframes spin {
  0%, 20% { opacity: 0; transform: scale(0.8); }
  50% { opacity: 1; transform: scale(1); }
  80%, 100% { opacity: 0; transform: scale(0.8); }
}

@keyframes pulse {
  0%, 100% { 
    transform: scale(0.8); 
    opacity: 0.6; 
  }
  50% { 
    transform: scale(1.2); 
    opacity: 0.3; 
  }
}

@keyframes centerPulse {
  0% { 
    transform: scale(1); 
    box-shadow: 0 0 20px rgba(79, 70, 229, 0.6);
  }
  100% { 
    transform: scale(1.1); 
    box-shadow: 0 0 30px rgba(79, 70, 229, 0.8);
  }
}

@keyframes float {
  0%, 100% { 
    transform: translateY(0px) scale(1);
    opacity: 0.7;
  }
  50% { 
    transform: translateY(-15px) scale(1.2);
    opacity: 1;
  }
}

@keyframes textShimmer {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.7; }
}

/* Responsive adjustments */
@media (max-width: 640px) {
  .brand-logo {
    width: 80px;
    height: 80px;
  }
  
  .logo-center {
    inset: 25px;
  }
  
  .logo-icon {
    width: 18px;
    height: 18px;
  }
  
  .app-progress {
    min-width: 250px;
  }
  
  .app-loading-text {
    font-size: 1rem;
  }
  
  .loading-spinner-wrapper {
    width: 60px;
    height: 60px;
  }
  
  .spinner-pulse {
    width: 30px;
    height: 30px;
    margin: -15px 0 0 -15px;
  }
  
  .spinner-center {
    width: 8px;
    height: 8px;
    margin: -4px 0 0 -4px;
  }
  
  .loading-text {
    font-size: 1rem;
  }
}
</style> 