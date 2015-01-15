<form class="form-inline" role="form">
    <div class="form-group clearfix">
        <div class="pull-left ">
            <% if(pages > 0) { %>
            <a href="#" class="pagingPrev" data-page="1"><i class="icon-left-end"></i></a>
            <a href="#" class="pagingPrev" data-page="<%=prev%>"><i class="icon-left1"></i></a>
            <input type="text" value="<%=page%>" class="form-control pageInput" size="2" />of
            <span><%=pages%></span>
            <a href="#" class="pagingNext" data-page="<%=next%>"><i class="icon-right1"></i></a>
            <a href="#" class="pagingNext" data-page="<%=pages%>"><i class="icon-right-end"></i></a>
            <% } %>
        </div>
        <div class="pull-right">
            <!--<% if(typeof records != 'undefined' ){ %>
            <span><%=records%></span> <%=_('Records')%>,
            <% } %>-->
            <%=_('Per page')%>:
            <select class="form-control perPageSelect" style="width: 80px">
            <%
              var perPageRanges = [10,50,100,200];
              for ( var i in  perPageRanges) { %>
              <option value="<%=perPageRanges[i] %>" <% if(perPage == perPageRanges[i]) {print ('selected="selected"');} %>><%=perPageRanges[i] %></option>
              <% }
            %>
            </select>
        </div>
    </div>
</form>