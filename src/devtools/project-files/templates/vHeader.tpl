<!DOCTYPE html>
<html>
<head>
{% block header %}
	<base href="{{config["siteUrl"]}}">
	<meta charset="UTF-8">
	<link rel="icon" href="data:;base64,iVBORw0KGgo=">
	<title>%projectName%</title>
{% endblock %}
{% block css %}
	%cssFiles%
	{{css('css/style.css') | raw}}
{% endblock %}
</head>
<body>
{% block head %}
{% endblock %}