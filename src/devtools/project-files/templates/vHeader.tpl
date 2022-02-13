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
	{{css('css/style.css', {nonce: nonce})}}
{% endblock %}
</head>
<body>
{% block head %}
	<div class="main-container">
		<header>
			<div class="ui container">
				<div class="ui secondary inverted menu">
					<a href="/" class="active item">Home</a>
					<div class="item">{{ _self }}</div>
				</div>
			</div>
		</header>
		<main>
			<div class="ui container">
{% endblock %}
