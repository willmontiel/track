function DomManager() {
    this.rows = new Array();
    this.data = null;
    this.container = 'container';
}

DomManager.prototype.setData = function(data) {
    this.data = data;
};

DomManager.prototype.setRows = function(data) {
    this.rows = data;
};

DomManager.prototype.setContainer = function(container) {
    this.container = container;
};

DomManager.prototype.load = function() {
    this.refreshTable();
};

DomManager.prototype.createTable = function() {
    this.content = $('<table class="table table-bordered">\n\
                    <thead>\n\
                        <tr>\n\
                            <th>Fecha</th>\n\
                            <th>Nombre</th>\n\
                            <th>Tipo de visita</th>\n\
                            <th>Cliente</th>\n\
                            <th>Estado de batería</th>\n\
                            <th>Ubicación</th>\n\
                        </tr>\n\
                    </thead>\n\
                    <tbody id="content"></tbody>\n\
                </table>');
    
    $('#' + this.container).append(this.content);
};

DomManager.prototype.refreshTable = function() {
    console.log(this.rows);
    this.content.find('#content').empty('slow'); 
    for (var i = 0; i < this.rows.length; i++) {
        var row = $('<tr>\n\
                        <td>' + this.rows[i].date + '</td>\n\
                        <td>' + this.rows[i].name + '</td>\n\
                        <td>' + this.rows[i].visit + '</td>\n\
                        <td>' + this.rows[i].client + '</td>\n\
                        <td>' + this.rows[i].battery + '</td>\n\
                        <td>' + this.rows[i].location + '</td>\n\
                    </tr>');
        this.content.find('#content').append(row); 
    }
};