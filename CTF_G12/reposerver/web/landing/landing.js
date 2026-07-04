let timerPayment;
let timerContract;

function randomInt(a, b) {
    if (a > b) [a, b] = [b, a];
    let res = Math.round(Math.random() * (b-a) + a);
    return res;
}

async function flavorPayment() {
    // "Last payment received" flavor text
    let payElem = document.getElementById("numpay");
    let payOld = Number(payElem.innerHTML);
    let payNew = randomInt(700, 15000);
    let payRate = (payNew - payOld) / 50;

    let mspass = 0
    while (mspass != 50) {
        mspass += 1;
        payOld += payRate;
        payElem.innerHTML = Math.round(payOld);
        await new Promise(r => setTimeout(r, 10));
    }
    return;
}

async function flavorContract() {
    // "Last payment received" flavor text
    let contElem = document.getElementById("numcont");
    let contOld = Number(contElem.innerHTML);
    let contNew = randomInt(1000, 2000);
    let contRate = contNew / 50;

    let mspass = 0
    while (mspass != 50) {
        mspass += 1;
        contOld += contRate;
        contElem.innerHTML = Math.round(contOld);
        await new Promise(r => setTimeout(r, 10));
    }
    return;
}

async function setupFlavor() {
    timerPayment = setInterval(flavorPayment, randomInt(1000*2, 1000*3));
    timerContract = setInterval(flavorContract, randomInt(1000*0.7, 1000*2));
}