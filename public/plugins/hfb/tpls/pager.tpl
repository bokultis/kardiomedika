<form class="form-inline" role="form">
    <div class="form-group">
<% if(pages > 0) { %>
<a href="#" class="pagingPrev" data-page="1">&laquo;</a>
<a href="#" class="pagingPrev" data-page="<%=prev%>">&lsaquo;</a>
<input type="text" value="<%=page%>" class="form-control pageInput" size="2" style="width:40px" /> / 
<span><%=pages%></span>
<a href="#" class="pagingNext" data-page="<%=next%>">&rsaquo;</a>
<a href="#" class="pagingNext" data-page="<%=pages%>">&raquo;</a>
<% } %>
<% if(typeof records != 'undefined' ){ %>
<span><%=records%></span> <%=_('Records')%>,
<% } %>
<%=_('Per page')%>:
<select class="form-control perPageSelect" style="width: 70px">
<%
  var perPageRanges = [10,50,100,200];
  for ( var i in  perPageRanges) { %>
  <option value="<%=perPageRanges[i] %>" <% if(perPage == perPageRanges[i]) {print ('selected="selected"');} %>><%=perPageRanges[i] %></option>
  <% }
%>
</select>
</div>
</form>