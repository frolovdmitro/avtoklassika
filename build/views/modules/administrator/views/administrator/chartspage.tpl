<div class="chartpage" style="{{style}}"> {{BEGIN chart}} <script type="text/javascript">
        var data_{{name}} = [
            {{BEGIN points}}
                {xkey: '{{x_key}}',
                    {{BEGIN y_keys }}
                        {{name}}: {{value}}{{UNLESS _last}},{{END UNLESS}}
                    {{END y_keys}}
                }{{UNLESS _last }},{{END UNLESS}}
            {{END points}}
        ];

        Morris.Line({
            element: 'chart_{{name}}',
            data: data_{{name}},
            xkey: 'xkey',
            ykeys: [{{BEGIN y_keys}}'{{name}}'{{END y_keys}}{{UNLESS _last}},{{END UNLESS}}],
            labels: [{{BEGIN y_keys}}'{{caption}}'{{END y_keys}}{{UNLESS _last}},{{END UNLESS }}],
            lineColors: [{{BEGIN y_keys}}'{{color}}'{{END y_keys}}{{UNLESS _last}},{{END UNLESS }}],
            units: '$'
        });
    </script> <div id="chart_{{name}}" class="chart-line" {{IF style}}style="{{style}}"{{END style}}> </div> {{END chart}} </div>