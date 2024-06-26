<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prirodno Kretanje Stanovništva</title>
    <link rel="stylesheet" type="text/css" href="style/info.css" media="screen" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <link rel="shortcut icon" href="images/main.png">
    <script src="https://d3js.org/d3.v4.min.js"></script>
    <script src="https://d3js.org/topojson.v1.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
</head>

<body onload="sizeChange()">
<div class="bg-img">
        <div class="bg-text">
            <h1 id="naslov">Prirodno kretanje stanovništva u Hrvatskoj</h1>
        </div>
    </div>
    <h3 id="zupanija"></h3>
    <div id="infoContainer">
        <div class="row">
            <div class="column" id="colMap">
                <div style="text-align: center; width: 100%;">
                    <div class="dropdown">
                        <button type="button" class="btn btn-info dropdown-toggle" style="width: 200px;">
                            Tip migracije
                        </button>
                        <div class="dropdown-menu">
                            <button class="dropdown-item" type="button" onClick="changeType('doseljeni')">Doseljavanje</button>
                            <button class="dropdown-item" type="button" onClick="changeType('odseljeni')">Odseljavanje</button>
                        </div>
                    </div>
                    <div class="dropdown">
                        <button type="button" class="btn btn-info dropdown-toggle">
                            Prostor kretanja
                        </button>
                        <div class="dropdown-menu">
                            <button class="dropdown-item" type="button" onClick="changePlace('grad_opcina')">Grad/Općina</button>
                            <button class="dropdown-item" type="button" onClick="changePlace('zupanija')">Županija</button>
                            <button class="dropdown-item" type="button" onClick="changePlace('inozemstvo')">Inozemstvo</button>
                            <button class="dropdown-item" type="button" onClick="changePlace('ukupno')">Ukupno</button>
                        </div>
                    </div>

                    <div class="dropdown">
                        <a type="button" class="btn btn-info btnBack" href="index.php">Glavna stranica</a>
                    </div>
                </div>
                <h5 id="graphTitle"></h5>
            </div>
            <div class="column" id="aboutGraph">
                <h3 id="methodologyTitle">Metodološka objašnjenja</h3>
                <button id="translateButton" class="btn btn-primary">Prevedi na engleski</button>
                <div id="methodologyText"></div>
            </div>
        </div>
    </div>
    <script>
        //Dohvacanje vrijednosti iz lokalnog spremnika internet preglednika te provjera da nije rucno unesen url
        var zupanija = window.localStorage.getItem("zupanija");
        var tip_migracije = window.localStorage.getItem("tip_migracije");
        var prostor_kretanja = window.localStorage.getItem("prostor_kretanja");

        if (zupanija == null) {
            window.location = "/index.html"
        }

        if (tip_migracije == null) {
            window.localStorage.setItem("tip_migracije", "doseljeni");
            tip_migracije = "doseljeni";
        }

        if (prostor_kretanja == null) {
            window.localStorage.setItem("prostor_kretanja", "grad_opcina");
            prostor_kretanja = "grad_opcina";
        }

        //Postavljanje naslova zupanije i grafa
        document.getElementById("zupanija").innerHTML = zupanija;
        document.getElementById("graphTitle").innerHTML = getTitle();

        setGraph();

        function setGraph() {

            d3.json("kretanje_stanovnistva.json", function(error, data) {
        if (error) {
            console.error('Error loading the JSON file:', error);
            return;
        }

        // Raspon godina koje graf prikazuje
        var years = [2010, 2011, 2012, 2013, 2014, 2015, 2016, 2017, 2018, 2019, 2020, 2021, 2022];

        // Filtriranje podataka samo za trazenu zupaniju
        var filteredData = data.filter(function(place) {
            return normalize(place.zupanija) === normalize(zupanija);
        });
        // Osiguravanje ispravnog parsiranja numeričkih vrednosti
        filteredData.forEach(function(place) {
            place.doseljeni_grad_opcina = +place.doseljeni_grad_opcina;
            place.doseljeni_zupanija = +place.doseljeni_zupanija;
            place.doseljeni_inozemstvo = +place.doseljeni_inozemstvo;
            place.doseljeni_ukupno = +place.doseljeni_ukupno;
            place.odseljeni_grad_opcina = +place.odseljeni_grad_opcina;
            place.odseljeni_zupanija = +place.odseljeni_zupanija;
            place.odseljeni_inozemstvo = +place.odseljeni_inozemstvo;
            place.odseljeni_ukupno = +place.odseljeni_ukupno;
        });
                var dictionary = [];
                var parseTime = d3.timeParse("%Y");

                //Pretraga i dohvacanje samo onih vrijednosti koji se odnose na trazeni tip i godinu migracije
                years.forEach(function(item) {
                    var places = filteredData.filter(function(place) {
                        return place.godina == item;
                    })

                    var sum = 0;
                    places.forEach(function(item) {
                        sum += item[tip_migracije + "_" + prostor_kretanja];
                    })

                    dictionary.push({
                        godina: parseTime(item),
                        ukupno: sum,
                        prosjek: sum / places.length
                    })
                });

                //Trazenje min i max vrijednost y osi
                var max = d3.max(dictionary, function(d) {
                    return d.ukupno
                });
                var min = d3.min(dictionary, function(d) {
                    return d.ukupno
                });

                //Postavljanje dimenzija
                var margin = {
                        top: 20,
                        right: 20,
                        bottom: 110,
                        left: 40
                    },
                    margin2 = {
                        top: 430,
                        right: 20,
                        bottom: 40,
                        left: 40
                    },
                    width = 960 - margin.left - margin.right,
                    height = 500 - margin.top - margin.bottom,
                    height2 = 500 - margin2.top - margin2.bottom;

                //Dodavanje svg prostora za mapu
                var svg = d3.select("#colMap")
                    .append("svg")
                    .attr("viewBox", "0 -50 " + (width + 180) + " " + (height + margin.top + margin.bottom + 100))
                    .append("g")
                    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

                //Definiranje varijabli za opseg osi
                var x = d3.scaleTime().range([0, width]),
                    x2 = d3.scaleTime().range([0, width]),
                    y = d3.scaleLinear().range([height, 0]),
                    y2 = d3.scaleLinear().range([height2, 0]);

                //Definiranje x i y osi;
                var xAxis = d3.axisBottom(x),
                    xAxis2 = d3.axisBottom(x2),
                    yAxis = d3.axisLeft(y);

                //Varijabla za postavlja prostora uvecavanja grafa
                var brush = d3.brushX()
                    .extent([
                        [0, 0],
                        [width, height2]
                    ])
                    .on("brush end", brushed);

                //Varijabla za zumiranje i postavljanje granica zumiranja
                var zoom = d3.zoom()
                    .scaleExtent([1, 20])
                    .translateExtent([
                        [0, 0],
                        [width, height]
                    ])
                    .extent([
                        [0, 0],
                        [width, height]
                    ])
                    .on("zoom", zoomed);

                //Linije za prvi graf
                var line = d3.line()
                    .x(function(d) {
                        return x(d.godina);
                    })
                    .y(function(d) {
                        return y(d.ukupno);
                    });

                //Linije za pomocni graf
                var line2 = d3.line()
                    .x(function(d) {
                        return x2(d.godina);
                    })
                    .y(function(d) {
                        return y2(d.ukupno);
                    });

                //Dodavanje granica oko grafa kako graf ne bi izlazio izvan prostora s lijeve i desne strane pri zumiranju
                var clip = svg.append("defs").append("svg:clipPath")
                    .attr("id", "clip")
                    .append("svg:rect")
                    .attr("width", width)
                    .attr("height", height)
                    .attr("x", 0)
                    .attr("y", 0);

                //Dodavanje glavnog grafa
                var chart = svg.append("g")
                    .attr("class", "focus")
                    .attr("transform", "translate(" + margin.left + "," + margin.top + ")")
                    .attr("clip-path", "url(#clip)");

                //Dodavanje g elementa za fokusiran prostor
                var focus = svg.append("g")
                    .attr("class", "focus")
                    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

                //Dodavanje prostora pomocnog grafa
                var context = svg.append("g")
                    .attr("class", "context")
                    .attr("transform", "translate(" + margin2.left + "," + margin2.top + ")");

                //Postavljanje domene za glavni i pomocni graf x i y os
                x.domain(d3.extent(dictionary, function(d) {
                    return d.godina;
                }));
                y.domain(d3.extent(dictionary, function(d) {
                    return d.ukupno;
                }));
                x2.domain(x.domain());
                y2.domain(y.domain());


                //Dodavanje x-osi
                focus.append("g")
                    .attr("class", "axis axis--x")
                    .attr("transform", "translate(0," + height + ")")
                    .call(xAxis);

                //Dodavanje y-osi
                focus.append("g")
                    .attr("class", "axis axis--y")
                    .call(yAxis);

                //Dodavanje linearnog gradijenta za bolji prikaz promjena vrijednosti sa y osi
                svg.append("linearGradient")
                    .attr("id", "line-gradient")
                    .attr("gradientUnits", "userSpaceOnUse")
                    .attr("x1", 0)
                    .attr("y1", y(min))
                    .attr("x2", 0)
                    .attr("y2", y(max))
                    .selectAll("stop")
                    .data([{
                            offset: "10%",
                            color: "blue"
                        },
                        {
                            offset: "80%",
                            color: "red"
                        }
                    ])
                    .enter().append("stop")
                    .attr("offset", function(d) {
                        return d.offset;
                    })
                    .attr("stop-color", function(d) {
                        return d.color;
                    });

                //Dodavanje linije sa bojom ovisno o y vrijednosti na glavni graf
                chart.append("path")
                    .datum(dictionary)
                    .attr("fill", "none")
                    .attr("stroke", "url(#line-gradient)")
                    .attr("stroke-width", 4)
                    .attr("class", "line")
                    .attr("d", line);

                //Dodavanje linije sa bojom ovisno o y vrijednosti na pomocni graf
                context.append("path")
                    .datum(dictionary)
                    .attr("fill", "none")
                    .attr("stroke", "url(#line-gradient)")
                    .attr("stroke-width", 2.5)
                    .attr("class", "line")
                    .attr("d", line2);

                //Dodavanje x-osi na pomocni graf
                context.append("g")
                    .attr("class", "axis axis--x")
                    .attr("transform", "translate(0," + height2 + ")")
                    .call(xAxis2);

                //Dodavanje brusha na pomocni graf za odabira prostora za zumiranje
                context.append("g")
                    .attr("class", "brush")
                    .call(brush)
                    .call(brush.move, x.range());

                //Dodavanje nevidljivog kvadrata preko grafa za zumiranje
                svg.append("rect")
                    .attr("class", "zoom")
                    .attr("width", width)
                    .attr("height", height)
                    .attr("transform", "translate(" + margin.left + "," + margin.top + ")")
                    .call(zoom);

                //Dodavanje naziva na x-os
                svg.append("text")
                    .attr("transform",
                        "translate(" + (width + 105) + " ," +
                        (height + margin.top + 15) + ")")
                    .style("text-anchor", "middle")
                    .style("font-size", "23px")
                    .text("Godina");

                //Dodavanje naziva na y-os
                svg.append("text")
                    .attr("y", 0)
                    .attr("x", 10)
                    .style("text-anchor", "middle")
                    .style("font-size", "23px")
                    .text("Broj ljudi");

                function brushed() {
                    if (d3.event.sourceEvent && d3.event.sourceEvent.type === "zoom") return; // ignore brush-by-zoom
                    var s = d3.event.selection || x2.range();
                    x.domain(s.map(x2.invert, x2));
                    chart.select(".line")
                        .attr("d", d3.line()
                            .x(function(d) {
                                return x(d.godina)
                            })
                            .y(function(d) {
                                return y(d.ukupno)
                            })
                        );

                    focus.select(".axis--x").call(xAxis);
                    svg.select(".zoom").call(zoom.transform, d3.zoomIdentity
                        .scale(width / (s[1] - s[0]))
                        .translate(-s[0], 0));
                }

                function zoomed() {
                    if (d3.event.sourceEvent && d3.event.sourceEvent.type === "brush") return; // ignore zoom-by-brush
                    var t = d3.event.transform;
                    x.domain(t.rescaleX(x2).domain());
                    chart.select(".line")
                        .attr("d", d3.line()
                            .x(function(d) {
                                return x(d.godina)
                            })
                            .y(function(d) {
                                return y(d.ukupno)
                            })
                        );

                    focus.select(".axis--x").call(xAxis);
                    context.select(".brush").call(brush.move, x.range().map(t.invertX, t));
                }

            });
        }
        //Promjena vrijednosti tipa migracije u lokalnom spremniku na internet pregledniku i refreshanje grafa
        function changeType(tip_migracije) {
            window.localStorage.setItem("tip_migracije", tip_migracije);
            location.reload();
        }

        //Promjena vrijednosti prostora kretanja u lokalnom spremniku na internet pregledniku i refreshanje grafa
        function changePlace(prostor_kretanja) {
            window.localStorage.setItem("prostor_kretanja", prostor_kretanja);
            location.reload();
        }

        function normalize(name) {
    return name.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
}

        //Postavljanje naslova grafa ovisno o odabranom tipu i godini migracije
        function getTitle() {
            if (prostor_kretanja == "grad_opcina") {
                if (tip_migracije == "doseljeni") {
                    return "Doseljeni iz drugog grada/općine iste županije";
                } else {
                    return "Odseljeni u drugi grad/općinu iste županije";
                }
            } else if (prostor_kretanja == "zupanija") {
                if (tip_migracije == "doseljeni") {
                    return "Doseljeni iz druge županije";
                } else {
                    return "Odseljeni u drugu županiju";
                }
            } else if (prostor_kretanja == "inozemstvo") {
                if (tip_migracije == "doseljeni") {
                    return "Doseljeni iz inozemstva";
                } else {
                    return "Odseljeni u inozemstvo";
                }
            } else {
                if (tip_migracije == "doseljeni") {
                    return "Ukupno doseljeni";
                } else {
                    return "Ukupno odseljeni";
                }
            }
        }

        //Promjena velicine containera ovisno o velicini internet preglednika
        function sizeChange() {
            if ($("#container").width() > 1200) {
                d3.select("g").attr("transform", "scale(" + $("#container").width() / 2200 + ")");
                $("svg").height($("#container").width() * 0.37);
            } else if ($("#container").width() < 1200 && $("#container").width() > 800) {
                d3.select("g").attr("transform", "scale(" + $("#container").width() / 2900 + ")");
                $("svg").height($("#container").width() * 0.438);
            } else {
                d3.select("g").attr("transform", "scale(" + $("#container").width() / 1500 + ")");
                $("svg").height($("#container").width() * 0.538);
            }
        }

        const textHr = [
            "<b>Doseljenim</b>, odnosno <b>odseljenim</b> stanovništvom smatra se stanovništvo koje je promijenilo uobičajeno mjesto stanovanja na području Republike Hrvatske ili koje je promijenilo uobičajenu državu stanovanja na razdoblje koje je ili se očekuje da će biti dugo najmanje godinu dana.",
            "Podaci o migracijama obuhvaćaju državljane Republike Hrvatske i strance na privremenome ili stalnom boravku u Republici Hrvatskoj.",
            "Statistika unutarnje migracije stanovništva prikuplja i obrađuje podatke o tijekovima migracije stanovništva unutar zemlje, tj. o broju i strukturi osoba koje su promijenile mjesto stanovanja unutar Republike Hrvatske u određenoj kalendarskoj godini.",
            "Statistika vanjske migracije stanovništva prikuplja i obrađuje podatke o tijekovima vanjske migracije, tj. o broju i strukturi osoba koje su promijenile uobičajenu državu stanovanja u određenoj kalendarskoj godini."
        ];

        const textEn = [
            "<b>Immigrated</b> or <b>emigrated</b> population refers to the population that has changed their usual place of residence within the Republic of Croatia or changed their usual country of residence for a period that is or is expected to be at least one year.",
            "Migration data includes citizens of the Republic of Croatia and foreigners on temporary or permanent residence in the Republic of Croatia.",
            "Internal migration statistics collect and process data on migration flows within the country, i.e., the number and structure of people who have changed their place of residence within the Republic of Croatia in a given calendar year.",
            "External migration statistics collect and process data on the flows of external migration, i.e., the number and structure of people who have changed their usual country of residence in a given calendar year."
        ];

        let isEnglish = false;

        document.getElementById("translateButton").addEventListener("click", function() {
            const container = document.getElementById("methodologyText");
            container.innerHTML = "";
            const textArray = isEnglish ? textHr : textEn;
            let index = 0;

            function displaySentence() {
                if (index < textArray.length) {
                    const p = document.createElement("p");
                    p.innerHTML = textArray[index];
                    p.classList.add("sentence", "hidden");
                    container.appendChild(p);
                    setTimeout(() => {
                        p.classList.remove("hidden");
                        p.classList.add("visible");
                    }, 10);
                    index++;
                    setTimeout(displaySentence, 1000); // Vrijeme između prikazivanja rečenica
                } else {
                    document.getElementById("translateButton").textContent = isEnglish ? "Prevedi na engleski" : "Prevedi na hrvatski";
                    isEnglish = !isEnglish;
                }
            }

            displaySentence();
        });
    </script>

<footer class="bg-dark text-white text-center py-3">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <h5>About</h5>
                <p>This project visualizes the natural migration of population in Croatia.</p>
            </div>
            <div class="col-md-4">
                <h5>Navigation</h5>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-white">Home</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h5>Contact</h5>
                <p>Email: <a href="mailto:info@example.com" class="text-white">dgotal@etfos.hr</a></p>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col">
                <p>© 2024 Davor Gotal. All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>
</body>

</html>