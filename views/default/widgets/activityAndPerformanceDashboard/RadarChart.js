//Practically all this code comes from https://github.com/alangrafu/radar-chart-d3
//I only made some additions and aesthetic adjustments to make the chart look better
//(of course, that is only my point of view)
//Such as a better placement of the titles at each line end,
//adding numbers that reflect what each circular level stands for
//Not placing the last level and slight differences in color
//
//For a bit of extra information check the blog about it:
//http://nbremer.blogspot.nl/2013/09/making-d3-radar-chart-look-bit-better.html

/* OLA START */
var svgSpider = [];
/* OLA END */

var RadarChart = {
  draw: function(id, d, options){
	  var cfg = {
		 radius: 5,
		 w: 600,
		 h: 600,
		 factor: 1,
		 factorLegend: .85,
		 levels: 3,
	/* OLA START */
		//for adjusting default scaling behavior
		 minValue: 0,
		 scaleValuesAsNumbers: false,
		 scaleValueMultiplyFactor: 1,
		 typeOfData: 'CA',
		 dimensionsArr : [],
		 dimensionsHoverArr : [],
		 showData : true,
		 renderUsersArr : [],
	/* OLA END */
		 maxValue: 0,
		 radians: 2 * Math.PI,
		 opacityArea: 0.5,
		 ToRight: 5,
		 TranslateX: 80,
		 TranslateY: 30,
		 ExtraWidthX: 100,
		 ExtraWidthY: 100,
		 color: window.d3.scale.category10()
		};

/* OLA START */
	//added radarLayout to show radar chart instead of spider chart
	var radarLayout = false;

	//for adjusting default opacity behavior
	var spiderOpacityArr = [];
	spiderOpacityArr[0] = cfg.opacityArea;
	for (i = 1; i < 100; i++) {
		//till 100 to be sure
		spiderOpacityArr[i] = 0;
	}

	var opacity = [];
	opacity[0] = cfg.opacityArea;
	for (i = 1; i < 100; i++) {
		//till 100 to be sure
		opacity[i] = 0;
	}
	var mouseOverOpacity = 0.7;
	var mouseOverOpacity0 = 0.1;
	var mouseOutOpacity0 = cfg.opacityArea;
/* OLA END */

	if('undefined' !== typeof options){
	  for(var i in options){
		if('undefined' !== typeof options[i]){
		  cfg[i] = options[i];
		}
	  }
	}
	cfg.maxValue = Math.max(cfg.maxValue, d3.max(d, function(i){return d3.max(i.map(function(o){return o.value;}))}));
	var allAxis = (d[0].map(function(i, j){return i.axis}));
	var total = allAxis.length;
	var radius = cfg.factor*Math.min(cfg.w/2, cfg.h/2);
	var Format = d3.format('%');
	d3.select(id).select("svg").remove();

	var g = d3.select(id)
			.append("svg")
			.attr("width", cfg.w+cfg.ExtraWidthX)
			.attr("height", cfg.h+cfg.ExtraWidthY)
			.append("g")
			.attr("transform", "translate(" + cfg.TranslateX + "," + cfg.TranslateY + ")");
			;

/* OLA START */
	svgSpider[id] = g;
/* OLA END */

	var tooltip;

	//Circular segments
	for(var j=0; j<cfg.levels-1; j++){
	  var levelFactor = cfg.factor*radius*((j+1)/cfg.levels);
	  g.selectAll(".levels")
	   .data(allAxis)
	   .enter()
	   .append("svg:line")
	   .attr("x1", function(d, i){return levelFactor*(1-cfg.factor*Math.sin(i*cfg.radians/total));})
	   .attr("y1", function(d, i){return levelFactor*(1-cfg.factor*Math.cos(i*cfg.radians/total));})
	   .attr("x2", function(d, i){return levelFactor*(1-cfg.factor*Math.sin((i+1)*cfg.radians/total));})
	   .attr("y2", function(d, i){return levelFactor*(1-cfg.factor*Math.cos((i+1)*cfg.radians/total));})
	   .attr("class", "line")
	   .style("stroke", "grey")
	   .style("stroke-opacity", "0.75")
	   .style("stroke-width", "0.3px")
	   .attr("transform", "translate(" + (cfg.w/2-levelFactor) + ", " + (cfg.h/2-levelFactor) + ")");
	}

	//Text indicating at what % each level is
	for(var j=0; j<cfg.levels; j++){
/* OLA START */
	  var value;
	  if (cfg.scaleValuesAsNumbers) {
		value = cfg.scaleValueMultiplyFactor*((j+1)*(cfg.maxValue-cfg.minValue)/cfg.levels+cfg.minValue);
	  }
	  else {
	  	value = Format((j+1)*(cfg.maxValue-cfg.minValue)/cfg.levels+cfg.minValue);
	  }
/* OLA END */
	  var levelFactor = cfg.factor*radius*((j+1)/cfg.levels);
	  g.selectAll(".levels")
	   .data([1]) //dummy data
	   .enter()
	   .append("svg:text")
	   .attr("x", function(d){return levelFactor*(1-cfg.factor*Math.sin(0));})
	   .attr("y", function(d){return levelFactor*(1-cfg.factor*Math.cos(0));})
	   .attr("class", "legend")
	   .style("font-family", "sans-serif")
	   .style("font-size", "10px")
	   .attr("transform", "translate(" + (cfg.w/2-levelFactor + cfg.ToRight) + ", " + (cfg.h/2-levelFactor) + ")")
	   .attr("fill", "#737373")
/* OLA START */
//	   .text(Format((j+1)*cfg.maxValue/cfg.levels));
	   .text(value);
/* OLA END */
	}

	series = 0;

	var axis = g.selectAll(".axis")
			.data(allAxis)
			.enter()
			.append("g")
			.attr("class", "axis");

	axis.append("line")
		.attr("x1", cfg.w/2)
		.attr("y1", cfg.h/2)
		.attr("x2", function(d, i){return cfg.w/2*(1-cfg.factor*Math.sin(i*cfg.radians/total));})
		.attr("y2", function(d, i){return cfg.h/2*(1-cfg.factor*Math.cos(i*cfg.radians/total));})
		.attr("class", "line")
		.style("stroke", "grey")
		.style("stroke-width", "1px");
	axis.append("text")
		.attr("class", "legend")
		.text(function(d){return d})
		.style("font-family", "sans-serif")
		.style("font-size", "11px")
		.attr("text-anchor", "middle")
/* OLA START */
		.on('mouseover', function (d){
					var spiderWebHover = cfg.typeOfData + '_spiderwebhover';
					var value = d;
					var z = -1;
					for(var i=0; i<cfg.dimensionsArr.length; i++){
						if (cfg.dimensionsArr[i] == value) {
							z = i;
						}
					}
					if (z >= 0 && z < cfg.dimensionsHoverArr.length) {
						value = cfg.dimensionsHoverArr[z];
					}
					var labelY = d3.select(this).attr('y');
					var correction = 0;
					if (labelY > cfg.h/2) {
						correction = -20;
					}
					else {
						correction = 100;
					}
					var newX =  parseFloat(d3.select(this).attr('x')) + cfg.TranslateX;
					var newY =  parseFloat(d3.select(this).attr('y')) + cfg.TranslateY + correction ;
					document.getElementById(spiderWebHover).innerHTML = value;
					document.getElementById(spiderWebHover).style.left = '' + newX + 'px';
					document.getElementById(spiderWebHover).style.top = '' + newY + 'px';
//					document.getElementById(spiderWebHover).style.visibility = 'visible';
				  })
		.on('mouseout', function(){
					var spiderWebHover = cfg.typeOfData + '_spiderwebhover';
//					document.getElementById(spiderWebHover).style.visibility = 'hidden';
					document.getElementById(spiderWebHover).innerHTML = '';
				  })
/* OLA END */
		.attr("dy", "1.5em")
		.attr("transform", function(d, i){return "translate(0, -10)"})
		.attr("x", function(d, i){return cfg.w/2*(1-cfg.factorLegend*Math.sin(i*cfg.radians/total))-60*Math.sin(i*cfg.radians/total);})
		.attr("y", function(d, i){return cfg.h/2*(1-Math.cos(i*cfg.radians/total))-20*Math.cos(i*cfg.radians/total);});


	d.forEach(function(y, x){
	  dataValues = [];
	  g.selectAll(".nodes")
		.data(y, function(j, i){
/* OLA START */
//		  dataValues.push([
//			cfg.w/2*(1-(parseFloat(Math.max(j.value, 0))/cfg.maxValue)*cfg.factor*Math.sin(i*cfg.radians/total)),
//			cfg.h/2*(1-(parseFloat(Math.max(j.value, 0))/cfg.maxValue)*cfg.factor*Math.cos(i*cfg.radians/total))
//			]);
		  var newj = (-cfg.minValue + j.value)/(cfg.maxValue-cfg.minValue);
		  dataValues.push([
			cfg.w/2*(1-(parseFloat(Math.max(newj, 0))/cfg.maxValue)*cfg.factor*Math.sin(i*cfg.radians/total)),
			cfg.h/2*(1-(parseFloat(Math.max(newj, 0))/cfg.maxValue)*cfg.factor*Math.cos(i*cfg.radians/total))
			]);
/* OLA END */
		});
	  dataValues.push(dataValues[0]);

/* OLA START */
//added radarLayout to show radar chart instead of spider chart
	  if (!radarLayout) {
/* OLA END */
	  g.selectAll(".area")
					 .data([dataValues])
					 .enter()
					 .append("polygon")
					 .attr("class", "radar-chart-serie"+series)
					 .style("stroke-width", "2px")
					 .style("stroke", cfg.color(series))
					 .attr("points",function(d) {
						 var str="";
						 for(var pti=0;pti<d.length;pti++){
							 str=str+d[pti][0]+","+d[pti][1]+" ";
						 }
						 return str;
					  })
					 .style("fill", function(j, i){return cfg.color(series)})
/* OLA START */
//					 .style("fill-opacity", cfg.opacityArea)
					 .style("fill-opacity", opacity[x])
					 .style("visibility", function(){ if (cfg.showData && cfg.renderUsersArr[x]) return "visible"; else return "hidden";})
/* OLA END */
					 .on('mouseover', function (d){
/* OLA START */
/*										z = "polygon."+d3.select(this).attr("class");
										g.selectAll("polygon")
										 .transition(200)
										 .style("fill-opacity", 0.1);
										g.selectAll(z)
										 .transition(200)
										 .style("fill-opacity", .7);
*/
										if (x > 0) {
											var z0 = "polygon.radar-chart-serie0";
											g.selectAll(z0)
											 .transition(200)
											 .style("fill-opacity", mouseOverOpacity0);
										}
										z = "polygon."+d3.select(this).attr("class");
										g.selectAll(z)
										 .transition(200)
										 .style("fill-opacity", mouseOverOpacity);
									  })
/* OLA END */
					 .on('mouseout', function(){
/* OLA START */
/*										g.selectAll("polygon")
										 .transition(200)
										 .style("fill-opacity", cfg.opacityArea);
*/
										if (x > 0) {
											var z0 = "polygon.radar-chart-serie0";
											g.selectAll(z0)
											 .transition(200)
											 .style("fill-opacity", mouseOutOpacity0);
										}
										z = "polygon."+d3.select(this).attr("class");
										g.selectAll(z)
										 .transition(200)
										 .style("fill-opacity", opacity[x]);
/* OLA END */
					 });

//OLA START: added radarLayout to show radar chart instead of spider chart
	  }
//OLA END

	  series++;
	});
	series=0;


	d.forEach(function(y, x){
	  g.selectAll(".nodes")
		.data(y).enter()
		.append("svg:circle")
		.attr("class", "radar-chart-serie"+series)
		.attr('r', cfg.radius)
		.attr("alt", function(j){return Math.max(j.value, 0)})
/* OLA START */
//		.attr("cx", function(j, i){
//		  dataValues.push([
//			cfg.w/2*(1-(parseFloat(Math.max(j.value, 0))/cfg.maxValue)*cfg.factor*Math.sin(i*cfg.radians/total)),
//			cfg.h/2*(1-(parseFloat(Math.max(j.value, 0))/cfg.maxValue)*cfg.factor*Math.cos(i*cfg.radians/total))
//		]);
//		return cfg.w/2*(1-(Math.max(j.value, 0)/cfg.maxValue)*cfg.factor*Math.sin(i*cfg.radians/total));
//		})
//		.attr("cy", function(j, i){
//		  return cfg.h/2*(1-(Math.max(j.value, 0)/cfg.maxValue)*cfg.factor*Math.cos(i*cfg.radians/total));
//		})
		.attr("cx", function(j, i){
		  var newj = (-cfg.minValue + j.value)/(cfg.maxValue-cfg.minValue);
		  dataValues.push([
			cfg.w/2*(1-(parseFloat(Math.max(newj, 0))/cfg.maxValue)*cfg.factor*Math.sin(i*cfg.radians/total)),
			cfg.h/2*(1-(parseFloat(Math.max(newj, 0))/cfg.maxValue)*cfg.factor*Math.cos(i*cfg.radians/total))
		]);
		return cfg.w/2*(1-(Math.max(newj, 0)/cfg.maxValue)*cfg.factor*Math.sin(i*cfg.radians/total));
		})
		.attr("cy", function(j, i){
		  var newj = (-cfg.minValue + j.value)/(cfg.maxValue-cfg.minValue);
		  return cfg.h/2*(1-(Math.max(newj, 0)/cfg.maxValue)*cfg.factor*Math.cos(i*cfg.radians/total));
		})
/* OLA END */
		.attr("data-id", function(j){return j.axis})
		.style("fill", cfg.color(series)).style("fill-opacity", .9)
/* OLA START */
		.style("visibility", function(){ if (cfg.showData && cfg.renderUsersArr[x]) return "visible"; else return "hidden";})
/* OLA END */
		.on('mouseover', function (d){
/* OLA START */
					var value;
					if (cfg.scaleValuesAsNumbers) {
						value = cfg.scaleValueMultiplyFactor*d.value;
					}
					else {
						value = Format(d.value);
					}
/* OLA END */
					newX =  parseFloat(d3.select(this).attr('cx')) - 10;
					newY =  parseFloat(d3.select(this).attr('cy')) - 5;

					tooltip
						.attr('x', newX)
						.attr('y', newY)
						.text(value)
						.transition(200)
						.style('opacity', 1);

/* OLA START */
/*					z = "polygon."+d3.select(this).attr("class");
					g.selectAll("polygon")
						.transition(200)
						.style("fill-opacity", 0.1);
					g.selectAll(z)
						.transition(200)
						.style("fill-opacity", .7); */

					if (x > 0) {
						var z0 = "polygon.radar-chart-serie0";
						g.selectAll(z0)
						 .transition(200)
						 .style("fill-opacity", mouseOverOpacity0);
					}
					z = "polygon.radar-chart-serie" + x;
					g.selectAll(z)
					 .transition(200)
					 .style("fill-opacity", mouseOverOpacity);
/* OLA END */
				  })
		.on('mouseout', function(){
					tooltip
						.transition(200)
						.style('opacity', 0);
/* OLA START */
/*					g.selectAll("polygon")
						.transition(200)
						.style("fill-opacity", cfg.opacityArea);
*/
					if (x > 0) {
						var z0 = "polygon.radar-chart-serie0";
						g.selectAll(z0)
						 .transition(200)
						 .style("fill-opacity", mouseOutOpacity0);
					}
					z = "polygon.radar-chart-serie" + x;
					g.selectAll(z)
					 .transition(200)
					 .style("fill-opacity", opacity[x]);
/* OLA END */
				  })
		.append("svg:title")
/* OLA START */
//		.text(function(j){return Math.max(j.value, 0)});
		.text(function(j){return Math.max(j.value*cfg.scaleValueMultiplyFactor, 0)});
/* OLA END */

	  series++;
	});
	//Tooltip
	tooltip = g.append('text')
			   .style('opacity', 0)
			   .style('font-family', 'sans-serif')
			   .style('font-size', '13px');
  }
};