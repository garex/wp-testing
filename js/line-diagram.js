function WptLineDiagramOptions() {
    return this
        .setGoldenSizeRatio()
        .setGutter(32)
        .setDash('.')
        .setLineColor('#888')
        .showAxis('left')
        .showAxis('bottom')
        .setAxisColor('#999')
        .setAxisTextColor('#000')
        .setIsSmooth(true)
        .setSymbolColorOpacity(0.8)
        .setAnnotationRadius(6)
        .setAnnotationColor('#fff')
        .setAnnotationTextColor('#000')
    ;
};

WptLineDiagramOptions.prototype.sizeRatio = 1;
/**
 * @returns {WptLineDiagramOptions}
 */
WptLineDiagramOptions.prototype.setSizeRatio = function(value) {
    this.sizeRatio = value;
    return this;
};

/**
 * @returns {WptLineDiagramOptions}
 */
WptLineDiagramOptions.prototype.setGoldenSizeRatio = function() {
    var GOLDEN_RATIO = 1.618033988749895;
    return this.setSizeRatio(GOLDEN_RATIO);
};

WptLineDiagramOptions.prototype.gutter = 0;
/**
 * @returns {WptLineDiagramOptions}
 */
WptLineDiagramOptions.prototype.setGutter = function(value) {
    this.gutter = value;
    return this;
};

WptLineDiagramOptions.prototype.dash = '';
/**
 * Possible styles are: "", "-", ".", "-.", "-..", ". ", "- ", "--", "- .", "--.", "--.."
 * @returns {WptLineDiagramOptions}
 */
WptLineDiagramOptions.prototype.setDash = function(value) {
    this.dash = value;
    return this;
};

WptLineDiagramOptions.prototype.lineColor = '#000';
/**
 * @returns {WptLineDiagramOptions}
 */
WptLineDiagramOptions.prototype.setLineColor = function(value) {
    this.lineColor = value;
    return this;
};

WptLineDiagramOptions.prototype.axis = '0 0 0 0';
WptLineDiagramOptions.prototype.textAxisIndex = null;
WptLineDiagramOptions.prototype.valueAxisIndex = null;
/**
 * @returns {WptLineDiagramOptions}
 */
WptLineDiagramOptions.prototype.hideAxises = function() {
    this.textAxisIndex = null;
    this.valueAxisIndex = null;
    this.axis = '0 0 0 0';
    return this;
};
/**
 * Index to name: top right bottom left
 *
 * @returns {WptLineDiagramOptions}
 */
WptLineDiagramOptions.prototype.showAxis = function(name) {
    var map = {
        'top'   : 0,
        'right' : 1,
        'bottom': 2,
        'left'  : 3,
    };
    var index = map[name];
    if (typeof index === 'undefined') {
        return this;
    }
    var axises = this.axis.split(' ');
    if (axises.length != 4) {
        axises = [0, 0, 0, 0];
    }
    axises[index] = 1;
    this.axis = axises.join(' ');

    this.textAxisIndex = null;
    var axisesIndex = -1;
    for (var i = 0; i < 4; i++) {
        if (0 == axises[i]) {
            continue;
        }
        axisesIndex++;
        if (0 == i || 2 == i) {
            this.textAxisIndex = axisesIndex;
        } else {
            this.valueAxisIndex = axisesIndex;
        }
    }

    return this;
};

WptLineDiagramOptions.prototype.valueAxisTemplate = null;
/**
 * @returns {WptLineDiagramOptions}
 */
WptLineDiagramOptions.prototype.setValueAxisTemplate = function(value) {
    this.valueAxisTemplate = value;
    return this;
};

WptLineDiagramOptions.prototype.isSmooth = false;
/**
 * @returns {WptLineDiagramOptions}
 */
WptLineDiagramOptions.prototype.setIsSmooth = function(value) {
    this.isSmooth = value;
    return this;
};

WptLineDiagramOptions.prototype.symbolColorOpacity = 1;
/**
 * @returns {WptLineDiagramOptions}
 */
WptLineDiagramOptions.prototype.setSymbolColorOpacity = function(value) {
    this.symbolColorOpacity = value;
    return this;
};

WptLineDiagramOptions.prototype.annotationRadius = 5;
/**
 * @returns {WptLineDiagramOptions}
 */
WptLineDiagramOptions.prototype.setAnnotationRadius = function(value) {
    this.annotationRadius = value;
    return this;
};

WptLineDiagramOptions.prototype.annotationColor = '';
/**
 * @returns {WptLineDiagramOptions}
 */
WptLineDiagramOptions.prototype.setAnnotationColor = function(value) {
    this.annotationColor = value;
    return this;
};

WptLineDiagramOptions.prototype.annotationTextColor = '';
/**
 * @returns {WptLineDiagramOptions}
 */
WptLineDiagramOptions.prototype.setAnnotationTextColor = function(value) {
    this.annotationTextColor = value;
    return this;
};

WptLineDiagramOptions.prototype.axisColor = '#000';
/**
 * @returns {WptLineDiagramOptions}
 */
WptLineDiagramOptions.prototype.setAxisColor = function(value) {
    this.axisColor = value;
    return this;
};

WptLineDiagramOptions.prototype.axisTextColor = '#000';
/**
 * @returns {WptLineDiagramOptions}
 */
WptLineDiagramOptions.prototype.setAxisTextColor = function(value) {
    this.axisTextColor = value;
    return this;
};


function WptLineDiagram(wrapper, holder, data, $, options) {
    this
        .setupOptions(options)
        .setupPaper(wrapper, holder, $)
        .setupData(data)
        .createDiagram(holder, $)
        .addTextLabels()
        .addValueLabels()
    ;
};

WptLineDiagram.prototype.options = new WptLineDiagramOptions();

WptLineDiagram.prototype.setupOptions = function(options) {
    if (options) {
        this.options = options;
    }
    return this;
};

WptLineDiagram.prototype.setupPaper = function(wrapper, holder, $) {
    var $wrapper = $(wrapper),
        $holder  = $(holder),
        width    = $holder.width();

    $wrapper.height(this.calculateHeight($wrapper.width()));
    $holder.height(this.calculateHeight(width));

    this.paper = new ScaleRaphael($holder.attr('id'), width, $holder.height());

    var lastWidth = 0,
        me        = this;
    $(window).resize(function () {
        var currentWidth = $wrapper.width();
        if (currentWidth == lastWidth) {
            return;
        }
        lastWidth = currentWidth;
        $wrapper.height(me.calculateHeight(lastWidth));
        me.paper.changeSize(currentWidth, $wrapper.height());
    });

    return this;
};

WptLineDiagram.prototype.setupData = function(data) {
    var isSingle = 1 == data.length;
    this.data = data;
    if (isSingle) {
        var empty = {
            value   : data[0].value,
            title   : '',
            minimum : 0,
            maximum : 0
        };
        this.data.unshift(empty);
        this.data.push(empty);
    }

    this.dataX = [];
    this.dataY = [];
    this.dataMinimum = 0;
    this.dataMaximum = 0;

    for (var i = 0, iMax = this.data.length - 1; i <= iMax; i++) {
        if (i == 0) {
            this.data[i].hoverDir = 'right';
        } else if (i == iMax) {
            this.data[i].hoverDir = 'left';
        } else if (this.data[i].ratio <= 0.5) {
            this.data[i].hoverDir = 'up';
        } else {
            this.data[i].hoverDir = 'down';
        }

        this.dataX.push(i);
        this.dataY.push(this.data[i].value);

        this.dataMinimum = Math.min(this.dataMinimum, this.data[i].minimum);
        this.dataMaximum = Math.max(this.dataMaximum, this.data[i].maximum);
    }

    return this;
};

WptLineDiagram.prototype.createDiagram = function(holder, $) {
    var me = this;

    var axisYStep = this.dataMaximum - this.dataMinimum;
    while (axisYStep > 20) {
        axisYStep = Math.round(axisYStep / 10);
    }

    this.diagram  = this.paper.linechart(
        0, 0, $(holder).width(), $(holder).height(),
        [this.dataX, [0, 0]], [this.dataY, [this.dataMinimum, this.dataMaximum]],
        {
            gutter      : this.options.gutter,
            dash        : this.options.dash,
            colors      : [this.options.lineColor],
            symbol      : 'circle',
            shade       : true,
            axisxstep   : this.dataX.length - 1,
            axisystep   : axisYStep,
            axis        : this.options.axis,
            smooth      : this.options.isSmooth
        }
    );

    // Remove 2nd (fake) line's symbols
    this.diagram.symbols[1].remove();

    // Color symbols
    this.diagram.eachColumn(function() {
        if ('' == me.data[this.axis].title) {
            this.symbols.hide();
        } else {
            this.symbols.attr({fill: Raphael.getColor(me.options.symbolColorOpacity)});
        }
    });

    // Show annotations
    var hoverAttr = [
         {fill: me.options.annotationTextColor},
         {fill: me.options.annotationColor}
     ];
    this.diagram.hoverColumn(function () {
        var data = me.data[this.axis];
        if ('' == data.title) {
            return;
        }
        data.abbr.hide();
        var text = $.grep([
            data.ratioTitle,
            data.title.replace(/\s+/g, '\n').replace(/(\/)/g, '$1\n'),
            data.valueTitle
        ], function(n) { return (n); }).join('\n');
        this.hoverPopup = me.paper
            .popup(this.x, this.y[0], text, data.hoverDir)
            .attr(hoverAttr)
            .insertBefore(this);
    }, function () {
        var data = me.data[this.axis];
        if ('' == data.title) {
            return;
        }
        this.hoverPopup && this.hoverPopup.remove();
        data.abbr && data.abbr.show();
    });

    // Color axises
    this.diagram.axis.forEach(function(axis) {
        axis.attr({stroke: me.options.axisColor});
        axis.text.attr({fill: me.options.axisTextColor});
    });

    return this;
};

WptLineDiagram.prototype.addTextLabels = function() {
    if (null === this.options.textAxisIndex) {
        return this;
    }

    var data  = this.data,
        axis  = this.diagram.axis[this.options.textAxisIndex],
        texts = axis.text,
        lastIndex  = texts.length - 1,
        maxWidth   = axis.paper.width/lastIndex - 5,
        eMaxWidth  = maxWidth / 2 + this.options.gutter * 0.6,
        me         = this;

    var abbrAngle  = 45,
        abbrRadius = this.options.annotationRadius * 0.6,
        abbrAttr   = [
          {fill: this.options.annotationColor, stroke: this.options.lineColor},
          {fill: this.options.annotationTextColor}
       ];

    this.diagram.eachColumn(function() {
        if ('' == me.data[this.axis].title) {
            return;
        }
        var text  = me.data[this.axis].abbrTitle;
        me.data[this.axis].abbr = me.paper
            .tag(this.x, this.y[0], text, abbrAngle, abbrRadius)
            .attr(abbrAttr)
            .insertBefore(this.symbols)
        ;
    })

    texts.forEach(function(text, index) {
        var iMaxWidth = maxWidth;

        if (lastIndex > 0 && (index == 0 || index == lastIndex)) {
            iMaxWidth = eMaxWidth;
            text
                .attr('text-anchor', (index == 0) ? 'start' : 'end')
                .transform('t' + (me.options.gutter * ((index == 0) ? -0.5 : 0.5)) + ',0')
            ;
        }

        setTextToFit(text, data[index].title, iMaxWidth, ' ');
    });

    function setTextToFit(el, text, maxWidth, splitBy) {
        var words = text.split(splitBy);
        var add   = '';
        for (var i = words.length - 1; i >= 0; i--) {
            var current = words.join(splitBy) + add;
            el.attr('text', current);
            if (maxWidth > el.getBBox().width) {
                return;
            }
            if (i == 0 && splitBy != '') {
                setTextToFit(el, current, maxWidth, '');
                return;
            }
            add = '...';
            words.splice(-1, 1);
        }
    };

    return this;
};

WptLineDiagram.prototype.addValueLabels = function() {
    if (null === this.options.valueAxisIndex || null === this.options.valueAxisTemplate) {
        return this;
    }

    var data  = this.data,
        axis  = this.diagram.axis[this.options.valueAxisIndex];

    axis.text.forEach(function(text, index) {
        text.attr('text', this.options.valueAxisTemplate.replace('{value}', text.attr('text')));
    }, this);

    return this;
};

WptLineDiagram.prototype.calculateHeight = function(width) {
    return Math.round( width / this.options.sizeRatio);
};

WptLineDiagram.prototype.calculateWidth = function(height) {
    return Math.round(height * this.options.sizeRatio);
};
