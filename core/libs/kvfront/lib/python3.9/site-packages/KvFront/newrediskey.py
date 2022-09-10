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
from redis.commands.json.path import Path
import json

class NewRedisKey(object):

    def __init__(self, parentwindow, pparent, redisHelper):
        self.pparent = pparent
        self.redisHelper = redisHelper
        self.builderNewKey= Gtk.Builder()
        self.builderNewKey.add_from_file(FILE_UI_NEWREDISKEY)
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
        cbtype = self.builderNewKey.get_object("typecbt")
        
        txtview = self.builderNewKey.get_object("valuetextview")
        valuebuffer = txtview.get_buffer()
        value = valuebuffer.get_text(valuebuffer.get_start_iter(), valuebuffer.get_end_iter(), False)

        if key == "":
            msgdlg = Gtk.MessageDialog(self.dlgNewKey, 0, Gtk.MessageType.INFO,
                                    Gtk.ButtonsType.CLOSE, "please enter key name")
            msgdlg.run()
            msgdlg.destroy()
            return
        
        if value == "":
            msgdlg = Gtk.MessageDialog(self.dlgNewKey, 0, Gtk.MessageType.INFO,
                        Gtk.ButtonsType.CLOSE, "please enter value")
            msgdlg.run()
            msgdlg.destroy()
            return

        tree_iter = cbtype.get_active_iter()
        if tree_iter != None:
            model = cbtype.get_model()
            type = model[tree_iter][1]
            print("Selected: =%s" % type)
            if key == "":
                msgdlg = Gtk.MessageDialog(self.dlgNewKey, 0, Gtk.MessageType.INFO,
                                        Gtk.ButtonsType.CLOSE, "please enter key name")
                msgdlg.run()
                msgdlg.destroy()
                return

            if type == "1":
                ret = self.redisHelper.set(key, value , 0)
                print(ret)
                if ret is True:
                    msgdlg = Gtk.MessageDialog(self.dlgNewKey, 0, Gtk.MessageType.INFO,
                                            Gtk.ButtonsType.CLOSE, "add key successed")
                    msgdlg.run()
                    msgdlg.destroy()
                    self.dlgNewKey.destroy()
                else:
                    msgdlg = Gtk.MessageDialog(self.dlgNewKey, 0, Gtk.MessageType.WARNING,
                                            Gtk.ButtonsType.CLOSE, ret)
                    msgdlg.run()
                    msgdlg.destroy()
            elif type == "2":
                field_line = value.split("\n")
                mapping = {}
                for fl in field_line:
                    kv = fl.split(":",1)
                    mapping[kv[0]]=kv[1]
                ret = self.redisHelper.hmset(key, mapping)
                if ret == 0 or ret == 1:
                    msgdlg = Gtk.MessageDialog(self.dlgNewKey, 0, Gtk.MessageType.INFO,
                                            Gtk.ButtonsType.CLOSE, "add key successed")
                    msgdlg.run()
                    msgdlg.destroy()
                    self.dlgNewKey.destroy()
                else:
                    msgdlg = Gtk.MessageDialog(self.dlgNewKey, 0, Gtk.MessageType.WARNING,
                                            Gtk.ButtonsType.CLOSE, ret)
                    msgdlg.run()
                    msgdlg.destroy()
            elif type == "3":
                ret = self.redisHelper.lpush(key, value)
                if ret == 0 or ret == 1:
                    msgdlg = Gtk.MessageDialog(self.dlgNewKey, 0, Gtk.MessageType.INFO,
                                            Gtk.ButtonsType.CLOSE, "add key successed")
                    msgdlg.run()
                    msgdlg.destroy()
                    self.dlgNewKey.destroy()
                else:
                    msgdlg = Gtk.MessageDialog(self.dlgNewKey, 0, Gtk.MessageType.WARNING,
                                            Gtk.ButtonsType.CLOSE, ret)
                    msgdlg.run()
                    msgdlg.destroy()
            elif type == "4":
                ret = self.redisHelper.sadd(key, value)
                print(ret)
                if ret == 0 or ret == 1:
                    msgdlg = Gtk.MessageDialog(self.dlgNewKey, 0, Gtk.MessageType.INFO,
                                            Gtk.ButtonsType.CLOSE, "add key successed")
                    msgdlg.run()
                    msgdlg.destroy()
                    self.dlgNewKey.destroy()
                else:
                    msgdlg = Gtk.MessageDialog(self.dlgNewKey, 0, Gtk.MessageType.WARNING,
                                            Gtk.ButtonsType.CLOSE, ret)
                    msgdlg.run()
                    msgdlg.destroy()
            elif type == "5":
                field_line = value.split("\n")
                mapping = {}
                for fl in field_line:
                    kv = fl.split(":",1)
                    mapping[kv[0]]=kv[1]
                ret = self.redisHelper.zadd(key, mapping)
                print(ret)
                if ret == 0 or ret == 1:
                    msgdlg = Gtk.MessageDialog(self.dlgNewKey, 0, Gtk.MessageType.INFO,
                                            Gtk.ButtonsType.CLOSE, "add key successed")
                    msgdlg.run()
                    msgdlg.destroy()
                    self.dlgNewKey.destroy()
                else:
                    msgdlg = Gtk.MessageDialog(self.dlgNewKey, 0, Gtk.MessageType.WARNING,
                                            Gtk.ButtonsType.CLOSE, ret)
                    msgdlg.run()
                    msgdlg.destroy()
            elif type == "6":
                field_line = value.split("\n")
                line_num = 1
                mapping = {}
                for fl in field_line:
                    if line_num == 1:
                        id = fl
                    else:
                        kv = fl.split(":",1)
                        mapping[kv[0]]=kv[1]
                    line_num = line_num + 1
                print(mapping)
                try:
                    ret = self.redisHelper.xadd(key, id, mapping)
                    print(ret)
                    msgdlg = Gtk.MessageDialog(self.dlgNewKey, 0, Gtk.MessageType.INFO,
                                            Gtk.ButtonsType.CLOSE, "add key successed")
                    msgdlg.run()
                    msgdlg.destroy()
                    self.dlgNewKey.destroy()
                except Exception as err:
                    msgdlg = Gtk.MessageDialog(self.dlgNewKey, 0, Gtk.MessageType.WARNING,
                                            Gtk.ButtonsType.CLOSE, str(err))
                    msgdlg.run()
                    msgdlg.destroy()

            elif type == "7":
                lines = value.split("\n")
                add_key_failed = True
                for fl in lines:
                    kv = fl.split(":",1)
                    try:
                        ret = self.redisHelper.setbit(key, int(kv[0]), int(kv[1]))
                        print(ret)
                        if ret == 0:
                            add_key_failed = False
                        else:
                            msgdlg = Gtk.MessageDialog(self.dlgNewKey, 0, Gtk.MessageType.WARNING,
                                                    Gtk.ButtonsType.CLOSE, ret)
                            msgdlg.run()
                            msgdlg.destroy()

                    except Exception as err:
                        msgdlg = Gtk.MessageDialog(self.dlgNewKey, 0, Gtk.MessageType.WARNING,
                                                Gtk.ButtonsType.CLOSE, str(err))
                        msgdlg.run()
                        msgdlg.destroy()
                if add_key_failed is False:
                    msgdlg = Gtk.MessageDialog(self.dlgNewKey, 0, Gtk.MessageType.INFO,
                        Gtk.ButtonsType.CLOSE, "add key successed")
                    msgdlg.run()
                    msgdlg.destroy()
                    self.dlgNewKey.destroy()
                else:
                    msgdlg = Gtk.MessageDialog(self.dlgNewKey, 0, Gtk.MessageType.WARNING,
                        Gtk.ButtonsType.CLOSE, "add key failed")
                    msgdlg.run()
                    msgdlg.destroy()
                    self.dlgNewKey.destroy()

            elif type == "8":
                # print(Path.root_path())
                try:
                    ret = self.redisHelper.set_json(key, Path.root_path(), json.loads(value))
                    print(ret)
                    if ret is True:
                        msgdlg = Gtk.MessageDialog(self.dlgNewKey, 0, Gtk.MessageType.INFO,
                                                Gtk.ButtonsType.CLOSE, "add key successed")
                        msgdlg.run()
                        msgdlg.destroy()
                        self.dlgNewKey.destroy()
                    else:
                        msgdlg = Gtk.MessageDialog(self.dlgNewKey, 0, Gtk.MessageType.WARNING,
                                                Gtk.ButtonsType.CLOSE, ret)
                        msgdlg.run()
                        msgdlg.destroy()
                except Exception as err:
                    msgdlg = Gtk.MessageDialog(self.dlgNewKey, 0, Gtk.MessageType.WARNING,
                                                Gtk.ButtonsType.CLOSE, str(err))
                    msgdlg.run()
                    msgdlg.destroy()

            