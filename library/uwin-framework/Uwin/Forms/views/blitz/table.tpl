<div class="table-header">
    {{ IF search_visible }}
    <div class="search-block">
        <div class="field-wrap">
            <label for="search">{{ $lng_table_search }}</label>
            <input class="search" id="search" name="search" type="text" spellcheck="false" maxlength="64" value="{{ $quickFilter }}">
        </div>
        <div class="search-btn"></div>
    </div>
    {{ END search_visible}}
    <div class="page-control">
        <label>{{ $lng_table_page }} <span class="numPage">{{ $currentPage }}</span> {{ $lng_table_from }} <span class="countPages">{{ $countPages }}</span></label>
        <ul>
            <li class="prev"></li>
            <li class="next"></li>
        </ul>
    </div>
</div>
<table id="{{ $name }}" data-order-column="{{ draggableRow }}" data-pk-column="{{ pk }}">
    <thead>
        <tr>
        {{ IF draggableRow }}
            <th style="padding: 5px 0;"></th>
        {{ END IF }}
        {{ IF paginationRow }}
            <th style="width:10px;"></th>
        {{ END paginationRow }}
        {{ BEGIN columns }}
        {{ IF enabled }}{{ IF visible }}
            <th id="{{ $name }}" class="{{ $ordered }} {{ IF order }}order {{ $order_type }}{{ END order }}"
                {{ IF width }} style="width: {{ $width }}px;"{{ END width }}
                {{ IF hint }} abbr="{{ $hint }}"{{ END hint }}>
            {{ $caption }}</th>
            {{ END visible }}{{ END enabled }}
        {{ END }}
        {{ IF actions }}
            <th style="width:30px;"></th>
        {{ END actions }}
        </tr>
    </thead>
    <tbody>
        {{ includeScript('row.tpl') }}
    </tbody>
</table>

<div class="table-footer">
    {{ IF changedCountOnPage }}
    <div class="rowsOnPageContainer">
        <label for="rowsOnPage">{{ $lng_table_rowsOnPage }}:</label>
        <select name="rowsOnPage" id="rowsOnPage">
            <option value="1" {{ if(rowsOnPage1, 'selected="true"') }}>1</option>
            <option value="10" {{ if(rowsOnPage10, 'selected="true"') }}>10</option>
            <option value="25" {{ if(rowsOnPage25, 'selected="true"') }}>25</option>
            <option value="50" {{ if(rowsOnPage50, 'selected="true"') }}>50</option>
            <option value="100" {{ if(rowsOnPage100, 'selected="true"') }}>100</option>
            <option value="250" {{ if(rowsOnPage250, 'selected="true"') }}>250</option>
            <option value="500" {{ if(rowsOnPage500, 'selected="true"') }}>500</option>
            <option value="1000" {{ if(rowsOnPage1000, 'selected="true"') }}>1000</option>
            <option value="-1" {{ if(allRowsOnPage, 'selected="true"') }}>{{ $lng_table_all }}</option>
        </select>
    </div>
    {{ END changedCountOnPage }}

    <div class="page-control">
        <label for="numPage">{{ $lng_table_page }}</label>
        <input name="numPage" id="numPage" type="text" value="{{ $currentPage }}" />
        <label for="numPage">{{ $lng_table_from }} <span class="countPages">{{ $countPages }}</span></label>
        <ul>
            <li class="prev"></li>
            <li class="next"></li>
        </ul>
    </div>
</div>
