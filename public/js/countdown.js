const targetDate = new Date();
targetDate.setDate(targetDate.getDate() + 30);

function updateCountdown() {
    const currentDate = new Date();
    const timeLeft = targetDate - currentDate;

    const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
    const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

    document.getElementById('days').innerHTML = `<h3>${days}</h3><span>Days</span>`;
    document.getElementById('hours').innerHTML = `<h3>${hours}</h3><span>Hours</span>`;
    document.getElementById('minutes').innerHTML = `<h3>${minutes}</h3><span>Mins</span>`;
    document.getElementById('seconds').innerHTML = `<h3>${seconds}</h3><span>Secs</span>`;
}

setInterval(updateCountdown, 1000);

updateCountdown();
