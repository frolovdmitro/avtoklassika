<a class="basket_bar basket-bar{{UNLESS count}} basket-bar_state_disabled{{END UNLESS}}" href="/basket/#step=1">
    <small class="basket-bar__notifier">{{IF count}}{{count}}{{END IF}}{{UNLESS(count, '0')}}</small>
    <strong class="basket-bar__sum" data-cost="{{sum}}{{UNLESS(sum, '0')}}" data-usd-cost="{{sum_usd}}{{UNLESS(sum_usd, '0')}}">
        {{IF short_name == 'грн.'}}
        <span class="basket-bar__sum_type_int">{{sum_format}}{{UNLESS(sum, '0')}}</span>
        <small style="font-size:65%;" class="basket-bar__currency"> {{short_name}}</small>
        {{ELSE}}
        <span class="basket-bar__currency">{{IF short_name == 'P'}}
            <span class="rur">{{short_name}}</span>
            {{ELSE}}
            {{short_name}}
            {{END IF}}
            </span>
        <span class="basket-bar__sum_type_int">{{sum_format}}{{UNLESS(sum, '0')}}</span>
        {{END IF}}
    </strong>
</a>
