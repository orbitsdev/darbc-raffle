import './bootstrap';


import confetti from "canvas-confetti";

Livewire.on('raffle-winner-drawn', (data) => {
    // Trigger confetti
    confetti({
        particleCount: 150,
        spread: 70,
        origin: { y: 0.6 },
    });


    const audio = new Audio('/sounds/winner.mp3');
    audio.play();
});