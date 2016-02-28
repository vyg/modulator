$Content

<% loop $ActiveModules %>
<section class="$ClassName.Lowercase <% if $Odd %>odd<% else %>even<% end_if %> order-$Order $ExtraClasses">
	$Content
</section>
<% end_loop %>