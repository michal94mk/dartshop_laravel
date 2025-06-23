<template>
  <div class="admin-loading-container">
    <div class="admin-spinner-wrapper">
      <!-- Outer Rotating Ring -->
      <div class="outer-ring">
        <div class="ring-segment segment-1"></div>
        <div class="ring-segment segment-2"></div>
        <div class="ring-segment segment-3"></div>
      </div>
      
      <!-- Middle Pulse Ring -->
      <div class="middle-ring">
        <div class="pulse-wave wave-1"></div>
        <div class="pulse-wave wave-2"></div>
        <div class="pulse-wave wave-3"></div>
      </div>
      
      <!-- Inner Core -->
      <div class="inner-core">
        <div class="core-dot"></div>
        <div class="core-glow"></div>
      </div>
      
      <!-- Data Particles -->
      <div class="data-particles">
        <div class="particle particle-1"></div>
        <div class="particle particle-2"></div>
        <div class="particle particle-3"></div>
        <div class="particle particle-4"></div>
        <div class="particle particle-5"></div>
        <div class="particle particle-6"></div>
      </div>
    </div>
    
    <!-- Loading Text -->
    <div class="loading-indicator">
      <div class="progress-bar">
        <div class="progress-fill"></div>
      </div>
      <span class="loading-label">{{ message || 'Ładowanie danych...' }}</span>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AdminLoadingSpinner',
  props: {
    message: {
      type: String,
      default: null
    }
  }
}
</script>

<style scoped>
.admin-loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem 2rem;
  min-height: 200px;
}

.admin-spinner-wrapper {
  position: relative;
  width: 70px;
  height: 70px;
  margin-bottom: 2rem;
}

/* Outer Rotating Ring */
.outer-ring {
  position: absolute;
  inset: 0;
  border-radius: 50%;
  animation: rotateClockwise 3s linear infinite;
}

.ring-segment {
  position: absolute;
  inset: 0;
  border-radius: 50%;
  border: 2px solid transparent;
}

.segment-1 {
  border-top: 2px solid #4f46e5;
  border-right: 2px solid #4f46e5;
  opacity: 0.9;
  animation: fadeSegment 2s ease-in-out infinite;
}

.segment-2 {
  border-right: 2px solid #6366f1;
  border-bottom: 2px solid #6366f1;
  opacity: 0.7;
  animation: fadeSegment 2s ease-in-out infinite 0.7s;
}

.segment-3 {
  border-bottom: 2px solid #8b5cf6;
  border-left: 2px solid #8b5cf6;
  opacity: 0.5;
  animation: fadeSegment 2s ease-in-out infinite 1.4s;
}

/* Middle Pulse Ring */
.middle-ring {
  position: absolute;
  inset: 8px;
  border-radius: 50%;
}

.pulse-wave {
  position: absolute;
  inset: 0;
  border-radius: 50%;
  border: 1px solid rgba(79, 70, 229, 0.3);
  animation: pulseWave 2s ease-out infinite;
}

.wave-1 {
  animation-delay: 0s;
}

.wave-2 {
  animation-delay: 0.7s;
}

.wave-3 {
  animation-delay: 1.4s;
}

/* Inner Core */
.inner-core {
  position: absolute;
  top: 50%;
  left: 50%;
  width: 20px;
  height: 20px;
  margin: -10px 0 0 -10px;
}

.core-dot {
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, #4f46e5, #6366f1);
  border-radius: 50%;
  animation: coreBreath 2s ease-in-out infinite;
  box-shadow: 0 0 15px rgba(79, 70, 229, 0.4);
}

.core-glow {
  position: absolute;
  inset: -5px;
  background: radial-gradient(circle, rgba(79, 70, 229, 0.1) 0%, transparent 70%);
  border-radius: 50%;
  animation: glowPulse 1.5s ease-in-out infinite alternate;
}

/* Data Particles */
.data-particles {
  position: absolute;
  inset: -15px;
}

.particle {
  position: absolute;
  width: 3px;
  height: 3px;
  background: #4f46e5;
  border-radius: 50%;
  box-shadow: 0 0 6px rgba(79, 70, 229, 0.6);
}

.particle-1 {
  top: 5px;
  left: 50%;
  margin-left: -1.5px;
  animation: orbitParticle 4s linear infinite;
}

.particle-2 {
  top: 25%;
  right: 5px;
  animation: orbitParticle 4s linear infinite 0.7s;
}

.particle-3 {
  top: 50%;
  right: 5px;
  margin-top: -1.5px;
  animation: orbitParticle 4s linear infinite 1.4s;
}

.particle-4 {
  bottom: 25%;
  right: 5px;
  animation: orbitParticle 4s linear infinite 2.1s;
}

.particle-5 {
  bottom: 5px;
  left: 50%;
  margin-left: -1.5px;
  animation: orbitParticle 4s linear infinite 2.8s;
}

.particle-6 {
  top: 50%;
  left: 5px;
  margin-top: -1.5px;
  animation: orbitParticle 4s linear infinite 3.5s;
}

/* Loading Indicator */
.loading-indicator {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.75rem;
  min-width: 200px;
}

.progress-bar {
  width: 100%;
  height: 3px;
  background: rgba(79, 70, 229, 0.1);
  border-radius: 1.5px;
  overflow: hidden;
  backdrop-filter: blur(10px);
}

.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, #4f46e5, #6366f1, #8b5cf6);
  border-radius: 1.5px;
  animation: progressFlow 2s ease-in-out infinite;
  box-shadow: 0 0 10px rgba(79, 70, 229, 0.4);
}

.loading-label {
  font-size: 0.875rem;
  font-weight: 500;
  color: #64748b;
  text-align: center;
  animation: labelFade 2s ease-in-out infinite;
}

/* Animations */
@keyframes rotateClockwise {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

@keyframes fadeSegment {
  0%, 100% { opacity: 0.3; }
  50% { opacity: 1; }
}

@keyframes pulseWave {
  0% {
    transform: scale(0.8);
    opacity: 0.8;
  }
  100% {
    transform: scale(1.4);
    opacity: 0;
  }
}

@keyframes coreBreath {
  0%, 100% { 
    transform: scale(1);
    box-shadow: 0 0 15px rgba(79, 70, 229, 0.4);
  }
  50% { 
    transform: scale(1.1);
    box-shadow: 0 0 25px rgba(79, 70, 229, 0.6);
  }
}

@keyframes glowPulse {
  0% { opacity: 0.5; }
  100% { opacity: 0.8; }
}

@keyframes orbitParticle {
  0% {
    transform: scale(1) translateX(0);
    opacity: 0.7;
  }
  25% {
    transform: scale(1.2) translateX(3px);
    opacity: 1;
  }
  50% {
    transform: scale(1) translateX(0);
    opacity: 0.7;
  }
  75% {
    transform: scale(0.8) translateX(-3px);
    opacity: 0.5;
  }
  100% {
    transform: scale(1) translateX(0);
    opacity: 0.7;
  }
}

@keyframes progressFlow {
  0% {
    transform: translateX(-100%);
    opacity: 0;
  }
  50% {
    opacity: 1;
  }
  100% {
    transform: translateX(100%);
    opacity: 0;
  }
}

@keyframes labelFade {
  0%, 100% { opacity: 0.6; }
  50% { opacity: 1; }
}

/* Responsive Design */
@media (max-width: 640px) {
  .admin-spinner-wrapper {
    width: 50px;
    height: 50px;
  }
  
  .inner-core {
    width: 14px;
    height: 14px;
    margin: -7px 0 0 -7px;
  }
  
  .loading-indicator {
    min-width: 150px;
  }
  
  .loading-label {
    font-size: 0.75rem;
  }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
  .loading-label {
    color: #94a3b8;
  }
  
  .progress-bar {
    background: rgba(79, 70, 229, 0.2);
  }
}
</style> 