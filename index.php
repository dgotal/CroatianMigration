<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prirodno Kretanje Stanovništva</title>
    <link rel="stylesheet" type="text/css" href="style/index.css" media="screen" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <link rel="shortcut icon" href="images/main.png">
    <script src="scripts/d3.js"></script>
    <script src="scripts/jquery.js"></script>
    <script src="scripts/topojson.js"></script>
</head>

<body onload="sizeChange()">
<div class="bg-img">
        <div class="bg-text">
            <h1 id="naslov">Prirodno kretanje stanovništva u Hrvatskoj</h1>
        </div>
    </div>
    <div id="container">
        <div class="row">
            <div class="column" id="colMap">

                <div style="text-align: center; width: 100%;">
                    <div class="dropdown">
                        <button type="button" class="btn btn-dark btnReset" onClick="resetZoom()">Reset zoom</button>
                    </div>
                    <div class="dropdown">
                        <button type="button" class="btn btn-info dropdown-toggle" style="width: 180px;">
                            Tip migracije
                        </button>
                        <div class="dropdown-menu">
                            <button class="dropdown-item" type="button" onClick="changeType('doseljeni_ukupno')">Doseljavanje</button>
                            <button class="dropdown-item" type="button" onClick="changeType('odseljeni_ukupno')">Odseljavanje</button>
                        </div>
                    </div>
                    <div class="dropdown">
                        <button type="button" class="btn btn-info dropdown-toggle" style="width: 180px;">
                            Godina
                        </button>
                        <div class="dropdown-menu">
                            <button class="dropdown-item" type="button" onClick="changeYear(2011)">2011</button>
                            <button class="dropdown-item" type="button" onClick="changeYear(2012)">2012</button>
                            <button class="dropdown-item" type="button" onClick="changeYear(2013)">2013</button>
                            <button class="dropdown-item" type="button" onClick="changeYear(2014)">2014</button>
                            <button class="dropdown-item" type="button" onClick="changeYear(2015)">2015</button>
                            <button class="dropdown-item" type="button" onClick="changeYear(2016)">2016</button>
                            <button class="dropdown-item" type="button" onClick="changeYear(2017)">2017</button>
                            <button class="dropdown-item" type="button" onClick="changeYear(2018)">2018</button>
                            <button class="dropdown-item" type="button" onClick="changeYear(2019)">2019</button>
                            <button class="dropdown-item" type="button" onClick="changeYear(2020)">2020</button>
                            <button class="dropdown-item" type="button" onClick="changeYear(2021)">2021</button>
                            <button class="dropdown-item" type="button" onClick="changeYear(2022)">2022</button>
                        </div>
                    </div>

                </div>
                <h3 id="currentMigration"></h3>
                <script>
                    //Sirina i visina svg prostora
                    //Lock varijabla za onemogucavanje klikanja pokreni ako je vec pokrenuta animacija
                    //Places ce sadrzavati mjesta za odabranu godinu
                    var width = window.screen.width,
                    height = window.screen.height,
                    lock = false,
                    places;
                    var colors = ["#ADD8E6", "#FFD700", "#FFA500", "#FF4500", "#B22222", "#8B0000"];

                    var years = [2011, 2012, 2013, 2014, 2015, 2016, 2017, 2018, 2019, 2020, 2021, 2022];

                    // Define the div for the tooltip
                    var div = d3.select("body").append("div")
                        .attr("class", "tooltip")
                        .style("opacity", 0);

                    //Dohvacanje vrijednosti iz lokalnog spremnika na pregledniku i provjera te postavljanje defaultnih vrijednosti
                    var year = window.localStorage.getItem("year");
                    var type = window.localStorage.getItem("type");

                    if (year == null) {
                        window.localStorage.setItem("year", 2011);
                        year = 2011;
                    }
                    if (type == null) {
                        window.localStorage.setItem("type", "odseljeni_ukupno");
                        type = "odseljeni_ukupno";
                    }

                    //Inicijalizacije karte
                    setTitle();
                    setMap(year);


                    //Kreiranje projekcije
                    var projection = d3.geoMercator()
                        .center([0, 10])
                        .scale(6000)
                        .translate([17600, 4550])
                        .rotate([-180, 0]);

                    var path = d3.geoPath()
                        .projection(projection);

                    //Kreiraj svg prostor za dodavanje karte
                    var svg = d3.select("#colMap")
                        .append("svg")
                        .attr("width", "100%")
                        .attr("height", "100%")
                        .attr("class", "map")

                    //Postavljanje opsega zumiranja karte
                    const zoom = d3.zoom()
                        .scaleExtent([1, 14])
                        .on("zoom", zoomed);

                    var g = svg.append("g");
                    svg.call(zoom);

                    //Ucitaj topoJson Hrvatske
                    d3.json("cro_regv3.json", function(error, cro) {
    var data = topojson.feature(cro, cro.objects.layer1);
    g.selectAll("path.county")
        .data(data.features)
        .enter()
        .append("path")
        .attr("class", "county")
        .attr("id", function(d) {
            return d.id;
        })
        .attr("d", path)
        .style("stroke", "white")
        .style("stroke-width", 0.3)
        .on("click", clicked)
        .on("mouseover", function(d) {
            div.transition()
                .duration(300)
                .style("opacity", 1);
            div.html("Klikni za informacije o županiji")
                .style("left", (d3.event.pageX) + "px")
                .style("top", (d3.event.pageY - 28) + "px")
                .style("font-size", "max(0.4vh, 15px");
        })
        .on("mouseout", function(d) {
            div.transition()
                .duration(300)
                .style("opacity", 0);
        });
});
var animationTimer; // Čuva referencu na setTimeout
var animationIndex = 0; // Trenutni indeks u nizu godina
var animationActive = false; // Prati je li animacija trenutno aktivna

function startAnimation() {
    if (!animationActive) {
        animationActive = true;
        document.getElementById("playButton").disabled = true;
        document.getElementById("pauseButton").disabled = false;
        document.getElementById("stopButton").disabled = false;
        animateThroughYears(animationIndex);
    }
}

function pauseAnimation() {
    if (animationActive) {
        clearTimeout(animationTimer); // Zaustavlja trenutni setTimeout
        animationActive = false;
        document.getElementById("playButton").disabled = false;
        document.getElementById("pauseButton").disabled = true;
    }
}

function stopAnimation() {
    if (animationActive) {
        clearTimeout(animationTimer);
        animationActive = false;
        animationIndex = 0;
        document.getElementById("playButton").disabled = false;
        document.getElementById("pauseButton").disabled = true;
        document.getElementById("stopButton").disabled = true;
    }
}

function animateThroughYears(index) {
    if (index >= years.length || !animationActive) {
        stopAnimation();
        return;
    }
    setMap(years[index]);
    document.getElementById("currentMigration").textContent = getMigrationTitle(years[index]);
    animationIndex = index + 1;

    animationTimer = setTimeout(function() {
        animateThroughYears(animationIndex);
    }, 1400);
}

function getMigrationTitle(year) {
    let type = window.localStorage.getItem("type");
    return (type === "odseljeni_ukupno" ? "Odseljeni" : "Doseljeni") + " - " + year + ". godina";
}
d3.select(window).on("resize", sizeChange);

function setMap(year) {
    d3.json("kretanje_stanovnistva.json", function(error, data) {
        if (error) {
            console.error('Error loading the JSON file:', error);
            return;
        }

        places = data.filter(function(place) {
            return place.godina == year;
        });

        // Ensure all numeric values are parsed correctly
        places.forEach(function(place) {
            place.doseljeni_ukupno = +place.doseljeni_ukupno;
            place.odseljeni_ukupno = +place.odseljeni_ukupno;
            place.doseljeni_grad_opcina = +place.doseljeni_grad_opcina;
            place.doseljeni_zupanija = +place.doseljeni_zupanija;
            place.doseljeni_inozemstvo = +place.doseljeni_inozemstvo;
            place.odseljeni_grad_opcina = +place.odseljeni_grad_opcina;
            place.odseljeni_zupanija = +place.odseljeni_zupanija;
            place.odseljeni_inozemstvo = +place.odseljeni_inozemstvo;
        });

        // Uzima se najveca vrijednost ovisno o odabranom tipu migracije
        var max = d3.max(places, function(d) {
            return d[type];
        });

        // Skriva se info tab o gradu i zupaniji jer nisu jos odabrani
        (document.getElementsByClassName("zupanija_info"))[0].style.display = "none";
        (document.getElementsByClassName("grad_info"))[0].style.display = "none";

        // Skala koja prima domenu od 0 do najvece vrijednosti tipa migracije
        var indexScale = d3.scaleQuantize()
            .domain([0, 1000, 2000, 5000, 10000, max])
            .range([0, 1, 2, 3, 4, 5]);

        // Racunanje i postavljanje pojedinog opsega broja migracija
        document.getElementById("firstRange").innerHTML = Math.round(indexScale.invertExtent(5)[0]) + " - " + Math.round(indexScale.invertExtent(5)[1]);
        document.getElementById("secondRange").innerHTML = Math.round(indexScale.invertExtent(4)[0]) + " - " + (Math.round(indexScale.invertExtent(4)[1]) - 1);
        document.getElementById("thirdRange").innerHTML = Math.round(indexScale.invertExtent(3)[0]) + " - " + (Math.round(indexScale.invertExtent(3)[1]) - 1);
        document.getElementById("fourthRange").innerHTML = Math.round(indexScale.invertExtent(2)[0]) + " - " + (Math.round(indexScale.invertExtent(2)[1]) - 1);
        document.getElementById("fifthRange").innerHTML = Math.round(indexScale.invertExtent(1)[0]) + " - " + (Math.round(indexScale.invertExtent(1)[1]) - 1);
        document.getElementById("sixthRange").innerHTML = "0 - " + Math.round(indexScale.invertExtent(0)[1]);

        var circleScale = d3.scaleSqrt()
            .domain([1, max])
            .range([2, 20]);  

        places.sort((a, b) => circleScale(b[type]) - circleScale(a[type]));

        // Ukloni prethodne elemente
        g.selectAll("rect").remove();
        g.selectAll("circle").remove();

        // Kreiranje kvadratica i oznacavanje svako mjesta na karti
        g.selectAll("rect")
            .data(places)
            .enter()
            .append("rect")
            .attr("x", function(d) {
                return projection([parseFloat(d.lng), parseFloat(d.lat)])[0];
            })
            .attr("y", function(d) {
                return projection([parseFloat(d.lng), parseFloat(d.lat)])[1];
            })
            .attr("height", 1.7)
            .attr("width", 1.7)
            .style("fill", "black")
            .style("opacity", 0.95);

        g.selectAll("circle")
            .data(places)
            .enter()
            .append("circle")
            .attr("cx", function(d) {
                return projection([parseFloat(d.lng) + 0.0064, parseFloat(d.lat)])[0];
            })
            .attr("cy", function(d) {
                return projection([parseFloat(d.lng), parseFloat(d.lat) - 0.005])[1];
            })
            .attr("r", function(d) {
                return circleScale(d[type]);
            })
            .style("fill", function(d) {
                return colors[indexScale(d[type])];
            })
            .style("opacity", 0.4)
            .style("stroke", "black")
            .style("stroke-width", 0.1)
            .on("click", clickCity)
            .on("mouseover", function(d) {
                div.transition()
                    .duration(200)
                    .style("opacity", 1);
                div.html("Klikni za informacije (" + d.mjesto + ")")
                    .style("left", (d3.event.pageX) + "px")
                    .style("top", (d3.event.pageY - 28) + "px");
            })
            .on("mouseout", function(d) {
                div.transition()
                    .duration(300)
                    .style("opacity", 0);
            });
    });
}
//Fja pri kliku na grad, prikazuje info tab o gradu, a skriva onaj o zupaniji
function clickCity(d) {
    (document.getElementsByClassName("zupanija_info"))[0].style.display = "none";
    (document.getElementsByClassName("grad_info"))[0].style.display = "";
    document.getElementById("grad_info_zupanija").innerHTML = d.zupanija;
    document.getElementById("grad_info_mjesto").innerHTML = d.mjesto;
    document.getElementById("grad_info_doseljeno").innerHTML = d.doseljeni_ukupno;
    document.getElementById("grad_info_odseljeno").innerHTML = d.odseljeni_ukupno;
    }
                    //Fja pri kliku na zupaniju, prikazuje info tab o zupaniji, a skriva onaj o gradu
                    //Uz to priblizava kartu za bolji pregled zupanije i gradova
                    //Priblizavanje i dimenzije ovise o velicini prozora
                    function clicked(d) {
    const [
        [x0, y0],
        [x1, y1]
    ] = path.bounds(d);
    d3.event.stopPropagation();
    if ($("#colMap").width() > 1300) {
        let zoom = d3.zoom()
            .extent([
                [0, 0],
                [width, height]
            ])
            .scaleExtent([1, 23])
            .on("zoom", zoomed);
        svg.transition().duration(850).call(
            zoom.transform,
            d3.zoomIdentity
            .translate($("#colMap").width() / 5, $("#colMap").height() / 35.3)
            .scale(Math.min(3, 4.5 / Math.max((x1 - x0) / $("#colMap").width() / 9.5, (y1 - y0) / $("#colMap").height())))
            .translate(-(x0 + x1) / 2.5159, -(y0 + y1) / 2.7),
            d3.mouse(svg.node())
        );
    } else if ($("#colMap").width() > 1000) {
        let zoom = d3.zoom()
            .scaleExtent([1, 73])
            .on("zoom", zoomed);
        svg.transition().duration(850).call(
            zoom.transform,
            d3.zoomIdentity
            .translate($("#colMap").width() / 5, $("#colMap").height() / 35.3)
            .scale(Math.min(4, 4.5 / Math.max((x1 - x0) / $("#colMap").width() / 9.5, (y1 - y0) / $("#colMap").height())))
            .translate(-(x0 + x1) / 2.4159, -(y0 + y1) / 2.4),
            d3.mouse(svg.node())
        );
    } else if ($("#colMap").width() > 900) {
        svg.transition().duration(850).call(
            zoom.transform,
            d3.zoomIdentity
            .translate($("#colMap").width() / 5, $("#colMap").height() / 24.3)
            .scale(Math.min(15, 0.4 / Math.max((x1 - x0) / $("#colMap").width() * 0.758, (y1 - y0) / $("#colMap").height())))
            .translate(-(x0 + x1) / 2.24, -(y0 + y1) / 3),
            d3.mouse(svg.node())
        );
    } else if ($("#colMap").width() > 800) {
        svg.transition().duration(850).call(
            zoom.transform,
            d3.zoomIdentity
            .translate($("#colMap").width() / 5, $("#colMap").height() / 24.3)
            .scale(Math.min(15, 0.4 / Math.max((x1 - x0) / $("#colMap").width() * 0.758, (y1 - y0) / $("#colMap").height())))
            .translate(-(x0 + x1) / 2.34, -(y0 + y1) / 2),
            d3.mouse(svg.node())
        );
    } else if ($("#colMap").width() > 600) {
        svg.transition().duration(850).call(
            zoom.transform,
            d3.zoomIdentity
            .translate($("#colMap").width() / 5, $("#colMap").height() / 2.3)
            .scale(Math.min(15, 0.4 / Math.max((x1 - x0) / $("#colMap").width() * 0.758, (y1 - y0) / $("#colMap").height())))
            .translate(-(x0 + x1) / 2.34, -(y0 + y1) / 2),
            d3.mouse(svg.node())
        );
    } else if ($("#colMap").width() > 500) {
        svg.transition().duration(850).call(
            zoom.transform,
            d3.zoomIdentity
            .translate($("#colMap").width() / 5, $("#colMap").height() / 2.3)
            .scale(Math.min(15, 0.4 / Math.max((x1 - x0) / $("#colMap").width() * 0.758, (y1 - y0) / $("#colMap").height())))
            .translate(-(x0 + x1) / 2.34, -(y0 + y1) / 2),
            d3.mouse(svg.node())
        );
    } else if ($("#colMap").width() > 400) {
        svg.transition().duration(850).call(
            zoom.transform,
            d3.zoomIdentity
            .translate($("#colMap").width() / 5, $("#colMap").height() / 3.3)
            .scale(Math.min(15, 0.4 / Math.max((x1 - x0) / $("#colMap").width() * 0.758, (y1 - y0) / $("#colMap").height())))
            .translate(-(x0 + x1) / 2.34, -(y0 + y1) / 2.2),
            d3.mouse(svg.node())
        );
    } else if ($("#colMap").width() > 300) {
        svg.transition().duration(850).call(
            zoom.transform,
            d3.zoomIdentity
            .translate($("#colMap").width() / 5, $("#colMap").height() / 3.3)
            .scale(Math.min(15, 0.4 / Math.max((x1 - x0) / $("#colMap").width() * 0.758, (y1 - y0) / $("#colMap").height())))
            .translate(-(x0 + x1) / 2.34, -(y0 + y1) / 1.7),
            d3.mouse(svg.node())
        );
    } else {
        svg.transition().duration(850).call(
            zoom.transform,
            d3.zoomIdentity
            .translate($("#colMap").width() / 5, $("#colMap").height() / 3.3)
            .scale(Math.min(15, 0.4 / Math.max((x1 - x0) / $("#colMap").width() * 0.758, (y1 - y0) / $("#colMap").height())))
            .translate(-(x0 + x1) / 2.34, -(y0 + y1) / 1.68),
            d3.mouse(svg.node())
        );
    }

    d3.selectAll('path').style('fill', "#197cb65d");
    d3.select(this).style("fill", "#115a99a4");
    document.getElementById("zupanija_info_naziv").innerHTML = d.properties.gn_name;
    document.getElementById("zupanija_info_egradani").innerHTML = d.properties.broj_korisnika;
    document.getElementById("zupanija_info_broj_gradova").innerHTML = countCities(d.properties.gn_name);
    document.getElementById("zupanija_info_doseljeno").innerHTML = countDoseljeno(d.properties.gn_name);
    document.getElementById("zupanija_info_odseljeno").innerHTML = countOdseljeno(d.properties.gn_name);
    (document.getElementsByClassName("zupanija_info"))[0].style.display = "";
    (document.getElementsByClassName("grad_info"))[0].style.display = "none";
}

                    //Promjena velicine containera ovisno o velicini internet preglednika
                    function sizeChange() {
                        if ($("#container").width() > 1200) {
                            d3.select("g").attr("transform", "scale(" + $("#container").width() / 3000 + ")");
                            $("svg").height($("#container").width() * 0.354);
                        } else if ($("#container").width() < 1200 && $("#container").width() > 800) {
                            d3.select("g").attr("transform", "scale(" + $("#container").width() / 2600 + ")");
                            $("svg").height($("#container").width() * 0.438);
                        } else {
                            d3.select("g").attr("transform", "scale(" + $("#container").width() / 1500 + ")");
                            $("svg").height($("#container").width() * 0.538);
                        }
                        resetZoom();
                        resetZoom();
                    }

                    //Funkcija za zumiranje prilikom skrolanja misem
                    function zoomed() {
                        g.attr("transform", d3.event.transform);
                    }

                    //Resetiranje zooma te centriranje karte
                    function resetZoom() {
                        if ($("#colMap").width() > 1500) {

                            var transform = d3.zoomIdentity.translate(300, -30).scale(1)
                        } else if ($("#colMap").width() > 1200) {

                            var transform = d3.zoomIdentity.translate(200, 0).scale(0.8)
                        } else if ($("#colMap").width() > 1100) {

                            var transform = d3.zoomIdentity.translate(200, -15).scale(0.8)
                        } else if ($("#colMap").width() > 1000) {

                            var transform = d3.zoomIdentity.translate(170, -50).scale(0.75)
                        } else if ($("#colMap").width() > 900) {

                            var transform = d3.zoomIdentity.translate(170, -40).scale(0.75)
                        } else if ($("#colMap").width() > 800) {

                            var transform = d3.zoomIdentity.translate(150, -30).scale(0.7)
                        } else if ($("#colMap").width() > 700) {

                            var transform = d3.zoomIdentity.translate(40, -50).scale(0.61)
                        } else if ($("#colMap").width() > 500) {

                            var transform = d3.zoomIdentity.translate(65, -35).scale(0.51)
                        } else if ($("#colMap").width() > 400) {

                            var transform = d3.zoomIdentity.translate(20, -27).scale(0.38)
                        } else if ($("#colMap").width() > 300) {
                            var transform = d3.zoomIdentity.translate(43, -14).scale(0.25)
                        } else {
                            var transform = d3.zoomIdentity.translate(23, -5).scale(0.19)
                        }
                        svg.transition().duration(850).call(zoom.transform, transform);
                        d3.selectAll('path').style('fill', null);
                        (document.getElementsByClassName("zupanija_info"))[0].style.display = "none";
                        (document.getElementsByClassName("grad_info"))[0].style.display = "none";
                    }

                    function normalize(name) {
    return name.toLowerCase().replace(/\s+/g, '-');
}                 
                    function countCities(zupanija) {
    var normalizedZupanija = normalize(zupanija);
    var broj_gradova = places.filter(function(place) {
        return normalize(place.zupanija) === normalizedZupanija;
    }).length;
    console.log("Broj većih gradova ili općina u " + zupanija + ": " + broj_gradova);
    return broj_gradova;
}


function countDoseljeno(zupanija) {
    var sumaDoseljenih = 0;
    var normalizedZupanija = normalize(zupanija);
    places.forEach(function(place) {
        var normalizedPlaceZupanija = normalize(place.zupanija);
        console.log("Provjera zupanije:", normalizedPlaceZupanija, normalizedZupanija, normalizedPlaceZupanija === normalizedZupanija);
        if (normalizedPlaceZupanija === normalizedZupanija) {
            sumaDoseljenih += place.doseljeni_ukupno;
        }
    });
    return sumaDoseljenih;
}

function countOdseljeno(zupanija) {
    var normalizedZupanija = normalize(zupanija);
    var sumaOdseljenih = 0;
    places.forEach(function(place) {
        if (normalize(place.zupanija) === normalizedZupanija) {
            sumaOdseljenih += place.odseljeni_ukupno;
        }
    });
    console.log("Ukupno odseljeno u " + zupanija + ": " + sumaOdseljenih);
    return sumaOdseljenih;
}


function countOdseljeno(zupanija) {
    var normalizedZupanija = normalize(zupanija);
    var sumaOdseljenih = 0;
    places.forEach(function(place) {
        if (normalize(place.zupanija) === normalizedZupanija) {
            console.log("Dodajem odseljene za " + place.mjesto + ": " + place.odseljeni_ukupno);
            sumaOdseljenih += place.odseljeni_ukupno;
        } else {
            console.log("Preskacem " + place.mjesto + " jer zupanija nije " + normalizedZupanija);
        }
    });
    console.log("Ukupno odseljeno u " + zupanija + ": " + sumaOdseljenih);
    return sumaOdseljenih;
}


                    //Promjena vrijednosti tipa migracije u lokalnom spremniku na internet pregledniku i refreshanje mape
                    function changeType(newType) {
    window.localStorage.setItem("type", newType);
    type = newType;
    reset();
}


                    //Promjena vrijednosti godine u lokalnom spremniku na internet pregledniku i refreshanje mape
                    function changeYear(year) {
                        window.localStorage.setItem("year", year);
                        reset();
                    }

                    //Pokretanje animacije koja prolazi kroz sve godine i prikazuje vrijednosti na karti
                    function startStop(index) {
                        if (lock && index == 0) {
                            return;
                        } else {
                            lock = true;
                            document.getElementById("btnAnimation").disabled = true;
                            setTimeout(function() {
                                if (index < years.length) {
                                    g.selectAll("circle").remove();
                                    setMap(years[index]);
                                    let type = window.localStorage.getItem("type");
                                    if (type == "odseljeni_ukupno") {
                                        document.getElementById("currentMigration").innerHTML = "Odseljeni - " + years[index] + ". godina";
                                    } else {
                                        document.getElementById("currentMigration").innerHTML = "Doseljeni - " + years[index] + ". godina";
                                    }

                                    index++;
                                    startStop(index);
                                } else {
                                    reset();
                                }
                            }, 1400);
                        }
                    }

                    //Postavljanje naslova prema odabranom tipu i godini migracije
                    function setTitle() {
                        if (window.localStorage.getItem("type") == "odseljeni_ukupno") {
                            document.getElementById("currentMigration").innerHTML = "Odseljeni - " + window.localStorage.getItem("year") + ". godina";
                        } else {
                            document.getElementById("currentMigration").innerHTML = "Doseljeni - " + window.localStorage.getItem("year") + ". godina";
                        }
                    }

                    //Resetiranje vrijednosti na mapi na defaultne, tj. one koje su spremljene u lokalnom spremniku preglednika
                    function reset() {
    let year = window.localStorage.getItem("year");
    let type = window.localStorage.getItem("type");
    setTitle();
    g.selectAll("circle").remove();
    setMap(year);
    lock = false;
    document.getElementById("btnAnimation").disabled = false;
}

                    function routeInfoPage() {
                        var zupanija = document.getElementById("zupanija_info_naziv").innerHTML;
                        window.localStorage.setItem("zupanija", zupanija);
                        window.location = "info.php"
                    }
                </script>
            <div id="mapControls" class="controls">
    <button id="playButton" onclick="startAnimation()" class="btn btn-success"><i class="fas fa-play"></i></button>
    <button id="pauseButton" onclick="pauseAnimation()" class="btn btn-warning" disabled><i class="fas fa-pause"></i></button>
    <button id="stopButton" onclick="stopAnimation()" class="btn btn-danger" disabled><i class="fas fa-stop"></i></button>
</div>
            </div>
            <div class="column-xs-6 legenda">
    <h3 style="text-align: center;">Legenda</h3>
    <ul class="list-group" style="text-align: center; width: 90%;">
        <h6 style="margin-top:10px">Oznake</h6>
        <li class="list-group-item d-flex justify-content-between align-items-center ">
            Grad ili općina
            <span class="rect" style="height:15px;width:15px;background-color: #030303;"></span>
        </li>
        <h6 style="margin-top:10px">Broj migracija</h6>
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <span id="firstRange"></span>
            <span class="dot" style="height:20px;width:20px;background-color: #8B0000;"></span>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <span id="secondRange"></span>
            <span class="dot" style="height:16px;width:16px;background-color: #B22222;"></span>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <span id="thirdRange"></span>
            <span class="dot" style="height:12px;width:12px;background-color: #FF4500;"></span>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <span id="fourthRange"></span>
            <span class="dot" style="height:8px;width:8px;background-color: #FFA500;"></span>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <span id="fifthRange"></span>
            <span class="dot" style="height:4px;width:4px;background-color: #FFD700;"></span>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <span id="sixthRange"></span>
            <span class="dot" style="height:4px;width:4px;background-color: #ADD8E6;"></span>
        </li>
    </ul>
</div>


            <div class="column-xs-6 zupanija_info">
                <h3 class="info" style="text-align: center;">Informacije o županiji</h3>
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <h6 style="margin-top:10px">Naziv</h6>
                        <span id="zupanija_info_naziv"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <h6 style="margin-top:10px">Broj stanovnika</h6>
                        <span id="zupanija_info_egradani"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <h6 style="margin-top:10px">Broj većih gradova ili općina</h6>
                        <span id="zupanija_info_broj_gradova"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <h6 style="margin-top:10px">Ukupno odseljeno</h6>
                        <span id="zupanija_info_odseljeno"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <h6 style="margin-top:10px">Ukupno doseljeno</h6>
                        <span id="zupanija_info_doseljeno"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <h6 style="margin-top:10px">Prikaz grafa za županiju</h6>
                        <button type="button" class="btn btn-outline-primary" onClick="routeInfoPage()">Prikaži</button>
                    </li>
                </ul>
            </div>
            <div class="column-xs-6 grad_info">
                <h3 class="info" style="text-align: center;">Informacije o gradu</h3>
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <h6 style="margin-top:10px">Županija</h6>
                        <span id="grad_info_zupanija"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <h6 style="margin-top:10px">Naziv grada</h6>
                        <span id="grad_info_mjesto"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <h6 style="margin-top:10px">Ukupno odseljeno</h6>
                        <span id="grad_info_odseljeno"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <h6 style="margin-top:10px">Ukupno doseljeno</h6>
                        <span id="grad_info_doseljeno"></span>
                    </li>
                </ul>
            </div>
        </div>

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

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js" integrity="sha384-q6EXHX8v5aXGLo/fyRBdKk/n4+t6z6fAldafO/Nd9JEc3QjmcFFfNodvMggS1zR" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgHcBjqbEM1qpcR6cDbExR59CZSm+5uEjE3zT4HM9BdK/c5JeV8" crossorigin="anonymous"></script>
</body>

</html>
