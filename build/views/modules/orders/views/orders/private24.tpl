<form action="https://api.privatbank.ua/p24api/ishop" method="POST"> <input type="text" name="amt" value="{{amount}}"/> <input type="text" name="ccy" value="UAH"/> <input type="hidden" name="merchant" value="{{merchant_id}}"/> <input type="hidden" name="order" value="{{order_id}}"/> <input type="hidden" name="details" value="{{description}}"/> <input type="hidden" name="ext_details" value=""/> <input type="hidden" name="pay_way" value="privat24"/> <input type="hidden" name="return_url" value="{{result_url}}"/> <input type="hidden" name="server_url" value="{{server_url}}"/> <input type="submit" value="Оплатить"/> </form>