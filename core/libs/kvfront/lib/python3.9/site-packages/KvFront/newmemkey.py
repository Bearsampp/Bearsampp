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
from KvFront.constants import *
from KvFront.redis_helper import *

class NewMemKey(object):

    def __init__(self, parentwindow, pparent, memHelper):
        self.pparent = pparent
        self.memHelper = memHelper
        self.builderNewKey= Gtk.Builder()
        self.builderNewKey.add_from_file(FILE_UI_NEWMEMKEY)
        self.builder = Gtk.Builder()
        self.builder.add_from_file(FILE_UI_MAIN)
        self.dlgNewKey = self.builderNewKey.get_object("NewKeyDlg")
        self.dlgNewKey.set_transient_for(parentwindow)
        
        self.builderNewKey.get_object("btnSaveKey").connect("clicked", self.btnSaveKey_clicked_cb)
        self.builderNewKey.get_object("btnClearKey").connect("clicked", self.btnClear_clicked_cb)
        self.dlgNewKey.show_all()
        
    def btnClear_clicked_cb(self,button):
        self.builderNewKey.get_object("keyentry").set_text("")
        self.builderNewKey.get_object("ttlentry").set_text("")
        txtview = self.builderNewKey.get_object("valuetextview")
        valuebuffer = txtview.get_buffer()
        valuebuffer.set_text("")
        
        
    def btnSaveKey_clicked_cb(self, button):
        key = self.builderNewKey.get_object("keyentry").get_text()
        
        txtview = self.builderNewKey.get_object("valuetextview")
        valuebuffer = txtview.get_buffer()
        value = valuebuffer.get_text(valuebuffer.get_start_iter(), valuebuffer.get_end_iter(), False)
        time = self.builderNewKey.get_object("ttlentry").get_text().strip()
        if time == "":
            time = "0"
        if key == "":
            msgdlg = Gtk.MessageDialog(self.dlgNewKey, 0, Gtk.MessageType.INFO,
                                    Gtk.ButtonsType.CLOSE, "please enter key name")
            msgdlg.run()
            msgdlg.destroy()
            return

        ret = self.memHelper.set(key, value, int(time))
        if ret is True:
            msgdlg = Gtk.MessageDialog(self.dlgNewKey, 0, Gtk.MessageType.INFO,
                                    Gtk.ButtonsType.CLOSE, "add key successed")
            msgdlg.run()
            msgdlg.destroy()
            self.dlgNewKey.destroy()
        else:
            msgdlg = Gtk.MessageDialog(self.dlgNewKey, 0, Gtk.MessageType.INFO,
                                    Gtk.ButtonsType.CLOSE, "add key failed")
            msgdlg.run()
            msgdlg.destroy()
        