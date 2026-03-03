const API = "index.php";

async function test(method, data = null) {
    const options = {
        method: method,
        headers: { "Accept": "application/json" }
    };
    if (data) {
        options.headers["Content-Type"] = "application/json";
        options.body = JSON.stringify(data);
    }

    try {
        const res = await fetch(API, options);
        const json = await res.json();
        document.getElementById("result").innerHTML = JSON.stringify(json, null, 2);
    } catch (e) {
        document.getElementById("result").innerHTML = "Errore: " + e.message;
    }
}

function testGET() { test("GET"); }
function testPOST() { test("POST", {nome: "prova", valore: 100}); }
function testPUT() { test("PUT", {nome: "aggiornato", valore: 50}); }
function testDELETE() { test("DELETE", {nome: "cancella", valore: 999}); }

