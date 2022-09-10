 #   Copyright (C) 2017-2022  Gavin W
 #   This program is free software: you can redistribute it and/or modify
 #   it under the terms of the GNU General Public License as published by
 #   the Free Software Foundation, either version 3 of the License, or
 #   (at your option) any later version.

 #   This program is distributed in the hope that it will be useful,
 #   but WITHOUT ANY WARRANTY; without even the implied warranty of
 #   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 #   GNU General Public License for more details.

 #   You should have received a copy of the GNU General Public License
 #   along with this program.  If not, see <http://www.gnu.org/licenses/>.
 
from gi.repository import Gtk
from KvFront.config import *
from KvFront.conninfo import *
from KvFront.redis_conninfo import *

class Connection(object):

    def __init__(self, parentwindow, pparent):
        self.pparent = pparent
        self.builderAddConn = Gtk.Builder()
        self.builderAddConn.add_from_file(FILE_UI_ADDSERVER)
        self.builder = Gtk.Builder()
        self.builder.add_from_file(FILE_UI_MAIN)
        self.dlgAddServer = self.builderAddConn.get_object("AddServerDlg")
        self.dlgAddServer.set_transient_for(parentwindow)
        
        self.builderAddConn.get_object("btnSaveConnection").connect("clicked", self.btnSaveConnection_clicked_cb)
        self.builderAddConn.get_object("btnClear").connect("clicked", self.btnClear_clicked_cb)
        self.builderAddConn.get_object("btnChoosePriKey").connect("clicked", self.btnChoosePriKey_clicked_cb)
        self.cfg = Config()
        self.dlgAddServer.show_all()
        
    def btnClear_clicked_cb(self,button):
        self.builderAddConn.get_object("nameentry").set_text("")
        self.builderAddConn.get_object("hostsentry").set_text("")

    def btnChoosePriKey_clicked_cb(self,button):
        dialog = Gtk.FileChooserDialog("Please choose a file of PrivateKey", self.dlgAddServer,
            Gtk.FileChooserAction.OPEN,
            (Gtk.STOCK_CANCEL, Gtk.ResponseType.CANCEL,
             "Select", Gtk.ResponseType.OK))
        # dialog.set_default_size(800, 400)
        response = dialog.run()
        if response == Gtk.ResponseType.OK:
            print("Folder selected: " + dialog.get_filename())
            self.builderAddConn.get_object("entry_sshpri").set_text(dialog.get_filename())
        elif response == Gtk.ResponseType.CANCEL:
            print("Cancel clicked")
        dialog.destroy()
        
        
    def btnSaveConnection_clicked_cb(self, button):
        print("add connection")
        name = self.builderAddConn.get_object("nameentry").get_text()
        host = self.builderAddConn.get_object("hostsentry").get_text()
        category = self.builderAddConn.get_object("categorycbt").get_active_text()
        password = self.builderAddConn.get_object("pwdentry").get_text()

        ssh_user = self.builderAddConn.get_object("entry_sshuser").get_text()
        ssh_pwd = self.builderAddConn.get_object("entry_sshpwd").get_text()
        ssh_prikey = self.builderAddConn.get_object("entry_sshpri").get_text()
        ssh_address = self.builderAddConn.get_object("entry_sshaddress").get_text()

        use_ssh = self.builderAddConn.get_object("chk_ssh").get_active()


        if name == "":
            msgdlg = Gtk.MessageDialog(self.dlgAddServer, 0, Gtk.MessageType.INFO,
                                    Gtk.ButtonsType.CLOSE, "please enter connection name")
            msgdlg.run()
            msgdlg.destroy()
            return
        if host == "":
            msgdlg = Gtk.MessageDialog(self.dlgAddServer, 0, Gtk.MessageType.INFO,
                                    Gtk.ButtonsType.CLOSE, "please enter connection endpoints")
            msgdlg.run()
            msgdlg.destroy()
            return
        if use_ssh:
            if ssh_user == "":
                msgdlg = Gtk.MessageDialog(self.dlgAddServer, 0, Gtk.MessageType.INFO,
                                    Gtk.ButtonsType.CLOSE, "please enter SSH user name")
                msgdlg.run()
                msgdlg.destroy()
                return
            if ssh_address == "":
                msgdlg = Gtk.MessageDialog(self.dlgAddServer, 0, Gtk.MessageType.INFO,
                                    Gtk.ButtonsType.CLOSE, "please enter SSH address")
                msgdlg.run()
                msgdlg.destroy()
                return

        addret = self.cfg.addServer(name, host, category, password, ssh_user, ssh_pwd, ssh_prikey, ssh_address)
        if addret==0:
             msgdlg = Gtk.MessageDialog(self.dlgAddServer, 0, Gtk.MessageType.INFO,
                                               Gtk.ButtonsType.CLOSE, "the server name is existed.")
             msgdlg.run()
             msgdlg.destroy()
        else:
            if category == "Memcached":
                p = Conninfo()
                p.name = name + "(" + category +")"
                p.hosts = host
                p.category = category
                p.password = password

                p.ssh_user = ssh_user
                p.ssh_pwd = ssh_pwd
                p.ssh_address = ssh_address
                p.ssh_prikey = ssh_prikey
            
                view =  self.pparent.builder.get_object("treeview2")
                model = view.get_model()
                model.append(None, (p,))
                self.dlgAddServer.destroy()
            elif category == "Redis Standalone" or category == "Redis Cluster":
                p = Conninfo()
                p.name = name + "(" + category +")"
                p.hosts = host
                p.category = category
                p.password = password

                view =  self.pparent.builder.get_object("treeview2")
                model = view.get_model()
                tp = model.append(None, (p,))
                for db_sn in range(0,16):
                    rp = RedisConninfo()
                    rp.name = "db" + str(db_sn)
                    rp.db_sn = db_sn
                    rp.hosts = host
                    rp.category = category
                    rp.password = password

                    rp.ssh_user = ssh_user
                    rp.ssh_pwd = ssh_pwd
                    rp.ssh_address = ssh_address
                    rp.ssh_prikey = ssh_prikey

                    model.append(tp,(rp,))
                self.dlgAddServer.destroy()