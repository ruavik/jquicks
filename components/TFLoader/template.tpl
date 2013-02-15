<button id="{{this->name}}_delete_files" onclick="{{@}}delete_selection();" title="Удалить выбранные файлы" disabled>-</button>
<input type="file" style="visibility:block; position:relative" 
       onchange="{{@}}handle_files(this.files);" id="{{this->name}}_input_file">
<div id="{{this->name}}_log"></div>
<button id="{{this->name}}_add_file" title="Загрузить файл" 
        onclick="document.getElementById('{{this->name}}_input_file').click()">+</button>
<table id="table-2">
<thead>
	<th>v</th>
        <th>Idx</th>
	<th>Origin</th>
        <th>Ext</th>
	<th>Filesize</th>
	<th>Filetype</th>
	<th>Date</th>
	<th>IP</th>
	<th>Title</th>
	<th>Text</th>
        <th>Tags</th>
	<th>Owner</th>
        <th>e</th>
</thead>
<tbody>
{{foreach from=this->view_model^->rows item=row name=loop}}
<tr>
  <td><input type="checkbox" onchange="{{@}}change_selection(this.checked,{{$row["idx"]}});"></td>
  <td>{{$row["idx"]}}</td>
  <td align="left"><img src="data/{{this->files_dir}}/{{$row["path"]}}">
<a href="javascript:{{@}}log_it('data/{{this->files_dir}}/{{$row["path"]}}')">{{$row["origin"]}}</a></td>
  <td>{{$row["extention"]}}</td>
  <td>{{$row["filesize"]}}</td>
  <td>{{$row["filetype"]}}</td>
  <td>{{$row["date"]}}</td>
  <td>{{$row["ip"]}}</td>
  <td>{{$row["title"]}}</td>
  <td>{{$row["text"]}}</td>
  <td>{{$row["tags"]}}</td>
  <td>{{$row["owner"]}}</td>
  <td><button disabled>Edit</button></td>
</tr>
{{/foreach}}
</tbody>
</table>
