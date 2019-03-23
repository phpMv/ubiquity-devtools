<!DOCTYPE html>
<html>
<head>
{% block header %}
	<base href="{{config["siteUrl"]}}">
	<meta charset="UTF-8">
	<title>%projectName%</title>
{% endblock %}
{% block css %}
%cssFiles%
{% endblock %}
</head>
<body>
{% block body %}
	<div class="ui container">
	</div>
{% endblock %}
{% block scripts %}
	%jsFiles%
{% endblock %}
</body>
</html>