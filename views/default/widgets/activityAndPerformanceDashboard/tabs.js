.d3-slider {
    position: relative;
    font-family: Verdana,Arial,sans-serif;
    font-size: 1.1em;
    border: 1px solid #aaaaaa;
    z-index: 2;
}

.d3-slider-horizontal {
    height: .8em;
}  

.d3-slider-vertical {
    width: .8em;
    height: 100px;
}      

.d3-slider-handle {
    position: absolute;
    width: 1.2em;
    height: 1.2em;
    border: 1px solid #d3d3d3;
    border-radius: 4px;
    background: #eee;
    background: linear-gradient(to bottom, #eee 0%, #ddd 100%);
    z-index: 3;
}

.d3-slider-handle:hover {
    border: 1px solid #999999;
}

.d3-slider-horizontal .d3-slider-handle {
    top: -.3em;
    margin-left: -.6em;
}

.d3-slider-axis {
    position: relative;
    z-index: 1;    
}

.d3-slider-axis-bottom {
    top: .8em;
}

.d3-slider-axis-right {
    left: .8em;
}

.d3-slider-axis path {
    stroke-width: 0;
    fill: none;
}

.d3-slider-axis line {
    fill: none;
    stroke: #aaa;
    shape-rendering: crispEdges;
}

.d3-slider-axis text {
    font-size: 11px;
}

.d3-slider-vertical .d3-slider-handle {
    left: -.25em;
    margin-left: 0;
    margin-bottom: -.6em;      
}