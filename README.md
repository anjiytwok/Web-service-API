JWS API
===============

JWS API (Joomla Web Service API) presents a simple way of creating a RESTful API using Joomla! Framework and the new [Unified Content Model](https://github.com/stefanneculai/joomla-platform/tree/content "Unified Content Model").

Every object in the API has a unique ID and it is considered beeing content. You can access the properties of an object by requesting [http://ws-api.cloudaccess.net/ws/www/RESOURCE/ID](http://ws-api.cloudaccess.net/ws/www/RESOURCE/ID). 


### Available Objects ###
+ [Application](#application)
+ [User](#user)	
+ [Screenshot](#screenshot)
+ [Tag](#tag)
+ [Comment](#tag)


### Application ##

An application has the following `fields`.
<table>
	<thead style="text-weight: bold;">
		<tr style="background: #F3F3F3;">
			<th>Name</th>
			<th>Description</th>
			<th>Returns</th>
		</tr>
	</thead>
		
	<tbody>
		<tr style="background: #FFF;">
			<td>`id`</td>
			<td>The application ID</td>
			<td>`integer`</td>
		</tr>
		
		<tr style="background: #FFF;">
			<td>`title`</td>
			<td>The title of the application</td>
			<td>`string`</td>
		</tr>
	</tbody>
</table>