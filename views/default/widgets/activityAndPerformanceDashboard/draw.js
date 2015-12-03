
function adjustWidgetStyle() {
  var widget = document.getElementById('tabs');
  for (i = 0;i < 4;i++) {
    if (widget != null) {
      widget = widget.parentNode;
    }
  }
  if (widget != null) {
    widget.style.position = 'relative';
    widget.style.top = '30px';
    widget.style.width = '958px';
  }
}

function CA_loadAndRender() {
  if (loadedAndRendered[0]) {
    return;
  }
  loadedAndRendered[0] = true;
  getData(typeOfDataArr[0]);
}

function PA_loadAndRender() {
  if (loadedAndRendered[1]) {
    return;
  }
  loadedAndRendered[1] = true;
  getData(typeOfDataArr[1]);
}

function MR_loadAndRender() {
  if (loadedAndRendered[2]) {
    return;
  }
  loadedAndRendered[2] = true;
  getData(typeOfDataArr[2]);
}

function GR_loadAndRender() {
  if (loadedAndRendered[3]) {
    return;
  }
  loadedAndRendered[3] = true;
  getData(typeOfDataArr[3]);
}

function getTypeOfDataIndex(typeOfData) {
  for (i = 0; i < typeOfDataArr.length; i++) {
    if (typeOfData == typeOfDataArr[i]) {
      return i;
    }
  }
  return -1;
}

function draw(typeOfData) {
  drawSpiderForTypeOfData(typeOfData);
  drawSliderForTypeOfData(typeOfData);
  if (typeOfData != 'CA' || showCABar) {
    drawBarsForTypeOfData(typeOfData);
  }
}

function drawSpiderForTypeOfData(typeOfData) {
  var index = getTypeOfDataIndex(typeOfData);
  if (index >= 0) {
    drawSpider(typeOfData);
    drawLegendForTypeOfData(typeOfData);
  }
}

function drawSpider(typeOfData) {
  var index = getTypeOfDataIndex(typeOfData);
  //Data
  var d = [];
  var d_numberofusers = 0;
  while (resultArr[index][0]['user'+d_numberofusers] != undefined) {
    d_numberofusers = d_numberofusers + 1;
  }
  $counter = 0;
  for (i = 0; i < d_numberofusers; i++) {
//        for (i = 0; i < 2; i++) {
    if (showUsersArr[index][i] == 1) {
      var dsub = [];
      for (j = 0; j < dimensionsArr[index].length; j++) {
        dsub[j] = {axis:dimensionsArr[index][j],value:resultArr[index][j]['user'+i][intervalNumberArr[index]-1]};
/*              var value = 0;
        if (i == 1 && index < 2) {
          value = 1 - d[0][j]['value'];
        }
        else {
          value = (Math.random() - 0.5)/2 + 0.5;
          if (index > 1) {
            value = Math.round(10*value)/10;
          }
        }
        dsub[j] = {axis:dimensionsArr[index][j],value:value}; */
      }
      d[$counter] = dsub;
      $counter = $counter + 1;
    }
  }

  var w = 300;
  var h = 300;
  //Options for the spiderweb, other than default
  var mycfg = {
    w: w,
    h: h,
/* OLA START */
    minValue: spiderMinScaleValue[index],
    scaleValuesAsNumbers: spiderScaleValuesAsNumbers[index],
    scaleValueMultiplyFactor: spiderScaleValueMultiplyFactor[index],
    typeOfData: typeOfData,
    dimensionsArr : dimensionsArr[index],
    dimensionsHoverArr : dimensionsHoverArr[index],
    showData : (index < 2 || showPerformanceData),
    renderUsersArr : renderUsersArr[index],
/* OLA END */
    maxValue: 1.00,
    levels: spiderNumberOfScaleValues[index],
    ExtraWidthX: 600,
    ExtraWidthY: 130,
    TranslateX: 110,
    TranslateY: 25
  }

  //Call function to draw the spiderweb
  //Will expect that data is in %'s
  RadarChart.draw("#" + typeOfDataArr[index] + "_spiderweb", d, mycfg);
}

function drawLegend(legendTitle, legendOptions, spiderId, index) {
  var w = 600;
  var h = 600;
  var colorscale = d3.scale.category10();
  var svg = d3.select('#' + spiderId)
    .selectAll('svg')
    .append('svg')
    .attr("width", w+300)
    .attr("height", h)

  //Create the title for the legend
  var text = svg.append("text")
    .attr("class", "title")
    .attr('transform', 'translate(90,0)')
    .attr("x", 470)
    .attr("y", 10)
    .attr("font-size", "12px")
    .attr("fill", "#404040")
    .text(legendTitle);

  //Initiate Legend
  var legend = svg.append("g")
    .attr("class", "legend")
    .attr("height", 100)
    .attr("width", 200)
    .attr('transform', 'translate(90,20)')
    ;
  //Create colour squares
  legend.selectAll('rect')
    .data(legendOptions)
    .enter()
    .append("rect")
    .attr("class", function(d, i){ return "legend-rectangle-serie" + i;})
    .attr("x", 475)
    .attr("y", function(d, i){ return i * 20;})
    .attr("width", 10)
    .attr("height", 10)
    .style("fill", function(d, i){ return colorscale(i);})
    .style("stroke", function(d, i){ return colorscale(i);})
    .style("fill-opacity", function(d, i){ if (renderUsersArr[index][i]) return 1; else return 0;})
    .style("cursor", function(d, i){ return "pointer";})
    .on('click', function(d, i){
    redrawLegendAndSpider(svg, svgSpider["#" + spiderId], index, i);
    })
    ;
  //Create text next to squares
  legend.selectAll('text')
    .data(legendOptions)
    .enter()
    .append("text")
    .attr("x", 488)
    .attr("y", function(d, i){ return i * 20 + 9;})
    .attr("font-size", "11px")
    .attr("fill", "#737373")
    .text(function(d) { return d; })
    .style("cursor", function(d, i){ return "pointer";})
    .on('click', function(d, i){
    redrawLegendAndSpider(svg, svgSpider["#" + spiderId], index, i);
    })
}

function redrawLegendAndSpider(svglegend, svgSpider, index, i) {
  var rect = svglegend.selectAll("rect.legend-rectangle-serie" + i);
  var polygon = svgSpider.selectAll("polygon.radar-chart-serie" + i);
  var circles = svgSpider.selectAll("circle.radar-chart-serie" + i);
  renderUsersArr[index][i] = !renderUsersArr[index][i];
  if (renderUsersArr[index][i]) {
    rect.style("fill-opacity", 1);
    polygon.style("visibility", "visible");
    circles.style("visibility", "visible");
  }
  else {
    rect.style("fill-opacity", 0);
    polygon.style("visibility", "hidden");
    circles.style("visibility", "hidden");
  }
}

function drawSliderForTypeOfData(typeOfData) {
  var index = getTypeOfDataIndex(typeOfData);
  if (index >= 1) {
    drawSlider(typeOfData, numberOfIntervalsArr[index], intervalNumberArr[index], typeOfDataArr[index] + "_slider");
  }
}

function drawSlider(typeOfData, numberOfIntervals, intervalNumber, sliderId) {
  var object = document.getElementById(sliderId);
  if (object != null) {
    object.style.visibility = 'visible';
  }
  d3.select('#'+sliderId).call(d3.slider()
    .on("slide", function(evt, value) {
      intervalNumber = value;
      if (intervalNumber > numberOfIntervals) {
        intervalNumber = numberOfIntervals;
      }
      var index = getTypeOfDataIndex(typeOfData);
      if (index >= 0) {
        intervalNumberArr[index] = intervalNumber;
      }
      drawSpiderForTypeOfData(typeOfData);
    })
    .value(intervalNumber)
//          .axis(true)
    .min(1)
    .max(numberOfIntervals));
  drawSliderScale(typeOfData, sliderId + 'scale');
}

function drawSliderScale(typeOfData, sliderScaleId) {
  var object = document.getElementById(sliderScaleId);
  if (object != null) {
    for (i = 0; i < numberOfMonths; i++) {
      var year = '';
      if (i == (numberOfMonths - 1)) {
        year = ' ' + lastYear;
      }
      drawSliderScaleElement(object, i, numberOfMonths, months[(lastMonthNr+(12-numberOfMonths)+i) % 12] + year);
    }
    object.style.visibility = 'visible';
  }
}

function showNoDataYet(typeOfData) {
  var object = document.getElementById(typeOfData + "_nodatayet");
  if (object != null) {
    object.style.visibility = 'visible';
  }
}

var maxBarValue = [];
maxBarValue['CA_bars_csv1'] = 10;
maxBarValue['GR_bars_csv1'] = 10;
maxBarValue['GR_bars_csv2'] = 10;

var valueLabelWidth = 40; // space reserved for value labels (right)
var barHeight = 20; // height of one bar
var barLabelWidth = 150; // space reserved for bar labels
var barLabelPadding = 5; // padding between bar and bar labels (left)
var gridLabelHeight = 18; // space reserved for gridline labels
var gridChartOffset = 3; // space between start of grid and first bar
var maxBarWidth = 250; // width of the bar with the max value

function drawBars(barsId, number, legendOptions, result) {

  var data = d3.csv.parse(d3.select('#' + barsId + '_csv' + number).text());
  if (!showMe) {
    var newdata = [];
    newdata[0] = data[1];
    data = newdata;
  }
  for (i = 0; i < data.length; i++) {
    data[i]['Name'] = legendOptions[i];
  }
  if (result != null) {
    for (i = 0; i < data.length; i++) {
      data[i]['Value'] = result[i];
    }
  }

  // accessor functions
  var barLabel = function(d) { return d['Name']; };
  var barValue = function(d) { return parseFloat(d['Value']); };
  var barBorderColor = function(d) { return d['BarBorderColor']; };
  var barFillColor = function(d) { return d['BarFillColor']; };

  // scales
  var yScale = d3.scale.ordinal().domain(d3.range(0, data.length)).rangeBands([0, data.length * barHeight]);
  var y = function(d, i) { return yScale(i); };
  var yText = function(d, i) { return y(d, i) + yScale.rangeBand() / 2; };
  var x = d3.scale.linear().domain([0, maxBarValue[barsId + '_csv' + number]]).range([0, maxBarWidth]);
  // svg container element
  var chart = d3.select('#' + barsId + number).append("svg")
    .attr('width', maxBarWidth + barLabelWidth + valueLabelWidth)
    .attr('height', gridLabelHeight + gridChartOffset + data.length * barHeight);
  // grid line labels
  var gridContainer = chart.append('g')
    .attr('transform', 'translate(' + barLabelWidth + ',' + gridLabelHeight + ')');
  gridContainer.selectAll("text").data(x.ticks(10)).enter().append("text")
    .attr("x", x)
    .attr("dy", -3)
    .attr("text-anchor", "middle")
    .text(String);
  // vertical grid lines
  gridContainer.selectAll("line").data(x.ticks(10)).enter().append("line")
    .attr("x1", x)
    .attr("x2", x)
    .attr("y1", 0)
    .attr("y2", yScale.rangeExtent()[1] + gridChartOffset)
    .style("stroke", "#ccc");
  // bar labels
  var labelsContainer = chart.append('g')
    .attr('transform', 'translate(' + (barLabelWidth - barLabelPadding) + ',' + (gridLabelHeight + gridChartOffset) + ')');
  labelsContainer.selectAll('text').data(data).enter().append('text')
    .attr('y', yText)
    .attr('stroke', 'none')
    .attr('fill', 'black')
    .attr("dy", ".35em") // vertical-align: middle
    .attr('text-anchor', 'end')
    .text(barLabel);
  // bars
  var barsContainer = chart.append('g')
    .attr('transform', 'translate(' + barLabelWidth + ',' + (gridLabelHeight + gridChartOffset) + ')');
  barsContainer.selectAll("rect").data(data).enter().append("rect")
    .attr('y', y)
    .attr('height', yScale.rangeBand())
    .attr('width', function(d) { return x(barValue(d)); })
    .attr('stroke', barBorderColor)
    .attr('fill', barFillColor);
  // bar value labels
  barsContainer.selectAll("text").data(data).enter().append("text")
    .attr("x", function(d) { return x(barValue(d)); })
    .attr("y", yText)
    .attr("dx", 3) // padding-left
    .attr("dy", ".35em") // vertical-align: middle
    .attr("text-anchor", "start") // text-align: right
    .attr("fill", "black")
    .attr("stroke", "none")
    .text(function(d) { return d3.round(barValue(d), 1); });
  // start line
  barsContainer.append("line")
    .attr("y1", -gridChartOffset)
    .attr("y2", yScale.rangeExtent()[1] + gridChartOffset)
    .style("stroke", "#000");
}

// load data for default tab of second main tab
function showSubtabs(tabnumber) {
  if (tabnumber == 2) {
    MR_loadAndRender();
  }
}

var loadedAndRendered = [];

jQuery(document).ready(function() {
  var jq1102 = jQuery.noConflict(true);

  //prevent conflict with jquery version of elgg
  //use jq1102 instead of $ to use widget jquery version
  //var jq1102 = jQuery.noConflict(true);
  jq1102( "#tabs" ).tabs();
  jq1102( "#subtabs1" ).tabs();
  jq1102( "#subtabs2" ).tabs();

  /* tabs functions */
  for (i = 0; i < typeOfDataArr.length; i++) {
    loadedAndRendered[i] = false;
  }

  getMRandGRresult();
  adjustWidgetStyle();

  // load data for default tab
  CA_loadAndRender();
});
