{{ UNLESS row }}
    <tr class="table-empty"><td colspan="{{ $count_columns }}">{{ $lng_table_empty }}</td></tr>
{{ END row }}
{{ BEGIN row }}
<tr{{ IF id }} id="{{ id }}" data-id="{{ id }}"{{ END id}}>
    {{ IF draggableRow }}
        <td class="draggable-cell"><div class="draggable-area"></div></td>
    {{ END IF }}

    {{ IF paginationRow }}
    <td style="text-align: right;">{{ $num }}</td>
    {{ END paginationRow }}

    {{ BEGIN field }}
    <td style="{{ IF align }}text-align: {{ $align }};{{ END align }}
               {{ IF style }}{{ $style}}{{ END style }}">
        {{ IF thm }}
            <a class="direct-link" target="_blank" href="{{ $value }}"><img src="{{ $thm }}" width="{{ $thmWidth }}" height="{{ $thmHeight }}" /></a>
        {{ END thm }}

        {{ IF bool }}
            {{ IF value }}
                <img src="/img/backend/yes.png" />
            {{ END value }}
        {{ END bool }}

        {{ IF text }}
            {{ $value }}
        {{ END text}}
    </td>
    {{ END }}
    {{ IF action }}
        <td style="text-align: center;white-space:nowrap;">
            {{ IF id }}
                {{ BEGIN action }}
                    {{IF newline}}<br>{{END newline}}<a title="{{ caption }}" class="row-action {{ name }}" href="{{ module_url }}{{ name }}/{{ id }}/">{{ caption }}</a>
                {{ END action }}
            {{ END id }}
        </td>
    {{ END action }}
</tr>
{{ END }}
{{ IF footer }}
<tr style="background: #EEF2F6;">
    {{ IF draggableRow }}<td></td>{{ END IF }}

    {{ IF paginationRow }}<td></td>{{ END paginationRow }}

    {{ BEGIN footer_field }}
    <td style="{{ IF align }}text-align: {{ $align }};{{ END align }}
                   {{ IF style }}{{ $style}}{{ END style }}">
    {{ UNLESS hide }}
        <span title="{{caption}}">{{ value }}</span>
    {{ END IF }}</td>
    {{ END footer_field }}

    {{ IF footer_actions }}<td></td>{{ END footer_actions }}
</tr>
{{ END footer }}
