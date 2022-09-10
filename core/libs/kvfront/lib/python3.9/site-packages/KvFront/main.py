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
 
#!/usr/bin/env python
# -*- coding: utf-8 -*-

import gi
# from prompt_toolkit import prompt
gi.require_version('GtkSource', '4')
from gi.repository import Gtk,GtkSource,Gio,GdkPixbuf
from KvFront.memcached import *
from KvFront.redis_helper import *
from KvFront.connection import *
from KvFront.newrediskey import *
from KvFront.newmemkey import *
from KvFront.conninfo import *
from KvFront.selected_obj import *
from KvFront.redis_conninfo import *
from KvFront.config import *
from KvFront.constants import *
from redis.commands.json.path import Path
import json
import sys
# gi.require_version('Gtk', '3.0') 


class KvFront():
    
    def __init__(self):
        
        GObject.type_register(Conninfo)
        GObject.type_register(RedisConninfo)
        GObject.type_register(SelectedObj)
        GObject.type_register(GtkSource.View)
        
        Gtk.Settings().set_property('gtk-dialogs-use-header', True)
        self.builder = Gtk.Builder()
        self.builderStats = Gtk.Builder()
        
        self.memip = "127.0.0.1"
        self.notebookdetail = None
        
        self.cfg = Config()
        servernames = self.cfg.getServerNames()
        
#         try:
        print(FILE_UI_MAIN)
        self.builder.add_from_file(FILE_UI_MAIN)
        self.builderStats.add_from_file(FILE_UI_DETAILPAGE)
#         except:
#             print("add from file failed")
#             sys.exit()
            
        self.window = self.builder.get_object("window1")
        self.btnrefreshstats = self.builder.get_object("btnRefreshStats")
        self.btnFlush = self.builder.get_object("btnFlush")
        self.btnNewKey = self.builder.get_object("btnNewKey")
        self.btnDeleteKey = self.builder.get_object("btnDeleteKey")
        self.btnrefreshstats.set_sensitive(False)
        self.btnFlush.set_sensitive(False)
        self.btnNewKey.set_sensitive(False)
        self.btnDeleteKey.set_sensitive(False)
    
        self.builder.connect_signals(self)

        self.store = Gtk.TreeStore(GObject.Object)
        for sn in servernames:
            serverinfo = self.cfg.getServerInfo(sn)
            #print(serverinfo)
            #print(serverinfo[0][1])
            p = Conninfo()
            p.name = sn + "(" + serverinfo[1][1]+ ")"
            p.hosts = serverinfo[0][1]
            p.category = serverinfo[1][1]
            p.password = serverinfo[2][1]
            p.ssh_user = serverinfo[3][1]
            p.ssh_pwd = serverinfo[4][1]
            p.ssh_prikey = serverinfo[5][1]
            p.ssh_address = serverinfo[6][1]
            tp = self.store.append(None, (p,))
            if serverinfo[1][1] == "Redis Standalone":
                for db_sn in range(0,16):
                    rp = RedisConninfo()
                    rp.name = "db" + str(db_sn)
                    rp.db_sn = db_sn
                    rp.hosts = serverinfo[0][1]
                    rp.category = serverinfo[1][1]
                    rp.password = serverinfo[2][1]

                    rp.ssh_user = serverinfo[3][1]
                    rp.ssh_pwd = serverinfo[4][1]
                    rp.ssh_prikey = serverinfo[5][1]
                    rp.ssh_address = serverinfo[6][1]
                    
                    self.store.append(tp,(rp,))
            if serverinfo[1][1] == "Redis Cluster":
                for db_sn in range(0,1):
                    rp = RedisConninfo()
                    rp.name = "db" + str(db_sn)
                    rp.db_sn = db_sn
                    rp.hosts = serverinfo[0][1]
                    rp.category = serverinfo[1][1]
                    rp.password = serverinfo[2][1]
                    rp.ssh_user = serverinfo[3][1]
                    rp.ssh_pwd = serverinfo[4][1]
                    rp.ssh_prikey = serverinfo[5][1]
                    rp.ssh_address = serverinfo[6][1]
                    self.store.append(tp,(rp,))

        view = self.builder.get_object("treeview2")
        view.set_model(self.store)
        
        renderer_connections = Gtk.CellRendererText()
        column_connections = Gtk.TreeViewColumn("Connections", renderer_connections, text=0)
        column_connections.set_cell_data_func(renderer_connections, self.get_connection_name)
        view.append_column(column_connections)
        column_connections.set_sort_column_id(0)

        # self.menuNewKey = self.builder.get_object("menuNewKey")
        # self.menuNewKey.props.relative_to = self.btnNewKey
        
        self.window.connect("destroy", Gtk.main_quit)
        self.window.show_all()
        
        self.memHelper = MemcachedHelper()
        self.redisHelper = RedisHelper()
        Gtk.main()
        
    def get_connection_name(self, column, cell, model, iter, data):
        cell.set_property('text', self.store.get_value(iter, 0).name)
    
    def init_detail_page_redis(self, builder):
        print("init detail page 4 redis")

        # image_status = builder.get_object("image_status")
        # image_status.set_from_pixbuf(GdkPixbuf.Pixbuf.new_from_file_at_size(os.path.join(DIR_ICON,"status.svg"), 32, 32))
        
        store = Gtk.ListStore(str, str)
        for i, column_title in enumerate(["key", "value"]):
            renderer = Gtk.CellRendererText()
            column = Gtk.TreeViewColumn(column_title, renderer, text=i)
            column.set_sizing(Gtk.TreeViewColumnSizing.GROW_ONLY)
            column.set_resizable(True)
            column.set_min_width(400)
            builder.get_object("TreeViewStats").append_column(column)
        builder.get_object("TreeViewStats").set_model(store)
        
        store1 = Gtk.ListStore(str, str, str, str)
        for i, column_title in enumerate(["key", "field", "value", "type"]):
            renderer = Gtk.CellRendererText()
            column = Gtk.TreeViewColumn(column_title, renderer, text=i)
            column.set_sizing(Gtk.TreeViewColumnSizing.GROW_ONLY)
            column.set_resizable(True)
            column.set_min_width(200)
#             column.set_max_width(300)
            builder.get_object("TreeViewResults").append_column(column)
        builder.get_object("TreeViewResults").set_model(store1)

        store2 = Gtk.ListStore(str, str)
        for i, column_title in enumerate(["key", "type"]):
            renderer = Gtk.CellRendererText()
            column = Gtk.TreeViewColumn(column_title, renderer, text=i)
            column.set_sizing(Gtk.TreeViewColumnSizing.GROW_ONLY)
            column.set_resizable(True)
            column.set_min_width(200)
#             column.set_max_width(300)
            builder.get_object("TreeViewResults1").append_column(column)
        builder.get_object("TreeViewResults1").set_model(store2)
        
        self.btnRun = builder.get_object("btnRun")
        self.btnRun.connect("clicked", self.btnRun_clicked_cb)

        self.btnFetchMore = builder.get_object("btnFetchMore")
        self.btnFetchMore.connect("clicked", self.btnFetchMore_clicked_cb)

        self.btnRemoveData = builder.get_object("btnRemoveData")
        self.hidBtnRemoveData = self.btnRemoveData.connect("clicked", self.btnRemoveData_clicked_cb, None, None)

        self.btnRemoveData2 = builder.get_object("btnRemoveData2")
        self.hidBtnRemoveData2 = self.btnRemoveData2.connect("clicked", self.btnRemoveData_clicked_cb)

        builder.get_object("btnRun1").connect("clicked", self.btnRun1_clicked_cb)
        builder.get_object("btnExecuteCmd").connect("clicked", self.btnExecuteCmd_clicked_cb)
        
        cbcmd = builder.get_object("cbcmd")
        storeCmd = builder.get_object("storecmd")
        storeCmd.append([1, "set"])
        storeCmd.append([2, "mget"])
        #storeCmd.append([3, "mset"])
        #storeCmd.append([4, "mget"])
        storeCmd.append([5, "hmset"])
        storeCmd.append([6, "hgetall"])
        storeCmd.append([7, "lrange"])
        storeCmd.append([8, "lpush"])
        # storeCmd.append([10, "xadd"])
        storeCmd.append([11, "xrange"])
        storeCmd.append([12, "sadd"])
        storeCmd.append([13, "smembers"])
        storeCmd.append([15, "zrange"])
        storeCmd.append([9, "delete"])
        cbcmd.set_active(0)

        sbcount = builder.get_object("sbcount")
        sbcount.set_range(1, sys.maxsize)
        sbcount.set_increments (1, 10)
        sbcount.set_value(500)

        sbfrom = builder.get_object("sbfrom")
        sbfrom.set_range(0, sys.maxsize)
        sbfrom.set_increments (1, 10)
        # sbfrom.set_value(0)
        self.btnRemoveData.set_sensitive(False)
        self.btnRemoveData2.set_sensitive(False)
        self.btnFetchMore.set_sensitive(False)

        self.selectedObj = None
        self.selected_mem_key = None
        self.selected_redis_key = None
        
    def init_detail_page(self, builder):
        print("init detail page")
        store = Gtk.ListStore(str, str)
        for i, column_title in enumerate(["key", "value"]):
            renderer = Gtk.CellRendererText()
            column = Gtk.TreeViewColumn(column_title, renderer, text=i)
            column.set_sizing(Gtk.TreeViewColumnSizing.GROW_ONLY)
            column.set_resizable(True)
            column.set_min_width(400)
            builder.get_object("TreeViewStats").append_column(column)
        builder.get_object("TreeViewStats").set_model(store)
        
        store1 = Gtk.ListStore(str, str)
        for i, column_title in enumerate(["key", "value"]):
            renderer = Gtk.CellRendererText()
            column = Gtk.TreeViewColumn(column_title, renderer, text=i)
            column.set_sizing(Gtk.TreeViewColumnSizing.GROW_ONLY)
            column.set_resizable(True)
            column.set_min_width(200)
#             column.set_max_width(300)
            if column_title == "value":
                renderer.set_property("editable", True)
                renderer.connect("edited", self.value_editing_done, store1)
            builder.get_object("TreeViewResults").append_column(column)
        builder.get_object("TreeViewResults").set_model(store1)
        
        self.btnRun4Mem = builder.get_object("btnRun")
        self.btnRun4Mem.connect("clicked", self.btnRun_clicked_cb)
        
        cbcmd = builder.get_object("cbcmd")
        storeCmd = builder.get_object("storecmd")
        storeCmd.append([1, "set"])
        storeCmd.append([2, "get"])
        storeCmd.append([3, "delete"])
        cbcmd.set_active(0)
        
    def treeview2_row_activated_cb(self, treeview, path, column):
        # model, treeiter = selection.get_selected()
        model = treeview.get_model()
        iter = model.get_iter(path)
        self.category = model[iter][0].category
        self.password = model[iter][0].password
        if iter != None:
            if self.category == "Memcached":
                print("You selected memcached", model[iter][0].hosts)
                self.memHelper.close()
                self.memip = model[iter][0].hosts

                self.ssh_user = model[iter][0].ssh_user
                self.ssh_pwd = model[iter][0].ssh_pwd
                self.ssh_prikey = model[iter][0].ssh_prikey
                self.ssh_address = model[iter][0].ssh_address
                    
                memiplist = self.memip.split(",")
                self.memHelper.connect(memiplist,self.ssh_user,self.ssh_pwd,self.ssh_prikey,self.ssh_address)

                self.btnrefreshstats.set_sensitive(True)
                self.btnFlush.set_sensitive(True)
                self.btnNewKey.set_sensitive(True)
                # paned1 = self.builder.get_object("paned1")
                builderStats = Gtk.Builder()
                builderStats.add_from_file(FILE_UI_DETAILPAGE)
                notebookdetail = builderStats.get_object("notebookdetail")
                boxCmd = self.builder.get_object("BoxCmd")

                if not self.notebookdetail is None:
                    Gtk.Container.remove(boxCmd, self.notebookdetail)
                    print("add notbook widget")
                else:
                    print("notebookdetail is null")
                boxCmd.add(notebookdetail)
                notebookdetail.set_property ("expand", True)
                self.notebookdetail = notebookdetail
                self.notebookdetail.connect('switch-page', self.notebookdetail_switch_page_cb)
                self.builderStats = builderStats
                self.init_detail_page(builderStats)
                ret = self.memHelper.stats()
                dictd = ret[0][1]
                store = Gtk.ListStore(str, str)
                for r in dictd:
                    store.append((r, dictd[r]))
                self.builderStats.get_object("TreeViewStats").set_model(store)
                self.builderStats.get_object("TreeViewResults").connect("row-activated", self.treeview_result_row_activated_cb);
            elif self.category == "Redis Standalone":
                if len(path) == 2:
                    print("You selected redis", model[iter][0].hosts)
                    self.redisHelper.close()
                    self.btnrefreshstats.set_sensitive(True)
                    self.btnFlush.set_sensitive(True)
                    self.btnNewKey.set_sensitive(True)
                    builderStats = Gtk.Builder()
                    builderStats.add_from_file(FILE_UI_DETAILPAGE4REDIS)
                    notebookdetail = builderStats.get_object("notebookdetail")
                    boxCmd = self.builder.get_object("BoxCmd")

                    if not self.notebookdetail is None:
                        Gtk.Container.remove(boxCmd, self.notebookdetail)
                        print("add notbook widget")
                    else:
                        print("notebookdetail is null")
                    boxCmd.add(notebookdetail)
                    notebookdetail.set_property ("expand", True)
                    self.notebookdetail = notebookdetail
                    self.notebookdetail.connect('switch-page', self.notebookdetail_switch_page_cb)
                    self.builderStats = builderStats
                    self.init_detail_page_redis(builderStats)

                    self.redisIpPort = model[iter][0].hosts
                    self.db_sn = model[iter][0].db_sn

                    self.ssh_user = model[iter][0].ssh_user
                    self.ssh_pwd = model[iter][0].ssh_pwd
                    self.ssh_prikey = model[iter][0].ssh_prikey
                    self.ssh_address = model[iter][0].ssh_address

                    connRet = self.redisHelper.connect(self.redisIpPort, self.password, self.db_sn,self.ssh_user,self.ssh_pwd,self.ssh_prikey,self.ssh_address)
                    if connRet == 0:
                        dictd = self.redisHelper.stats()
                        #print(dictd)
                        store = Gtk.ListStore(str, str)
                        for r in dictd:
                            store.append((r, str(dictd[r])))
                        self.builderStats.get_object("TreeViewStats").set_model(store)
                        self.builderStats.get_object("TreeViewResults").connect("row-activated", self.treeview_result_row_activated_cb)
                        self.builderStats.get_object("TreeViewResults1").connect("row-activated", self.treeview_result1_row_activated_cb)
                        self.builderStats.get_object("TreeViewResults2").connect("row-activated", self.treeview_result2_row_activated_cb)
                    else:
                        msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.WARNING,
                                                    Gtk.ButtonsType.CLOSE, connRet)
                        msgdlg.run()
                        msgdlg.destroy()
            elif self.category == "Redis Cluster":
                if len(path) == 2:
                    print("You selected redis", model[iter][0].hosts)
                    self.redisHelper.close()
                    self.btnrefreshstats.set_sensitive(True)
                    self.btnFlush.set_sensitive(True)
                    self.btnNewKey.set_sensitive(True)
                    builderStats = Gtk.Builder()
                    builderStats.add_from_file(FILE_UI_DETAILPAGE4REDIS)
                    notebookdetail = builderStats.get_object("notebookdetail")
                    boxCmd = self.builder.get_object("BoxCmd")

                    if not self.notebookdetail is None:
                        Gtk.Container.remove(boxCmd, self.notebookdetail)
                        print("add notbook widget")
                    else:
                        print("notebookdetail is null")
                    boxCmd.add(notebookdetail)
                    notebookdetail.set_property ("expand", True)
                    self.notebookdetail = notebookdetail
                    self.notebookdetail.connect('switch-page', self.notebookdetail_switch_page_cb)
                    self.builderStats = builderStats
                    self.init_detail_page_redis(builderStats)

                    self.redisIpPort = model[iter][0].hosts
                    self.db_sn = model[iter][0].db_sn

                    self.ssh_user = model[iter][0].ssh_user
                    self.ssh_pwd = model[iter][0].ssh_pwd
                    self.ssh_prikey = model[iter][0].ssh_prikey
                    self.ssh_address = model[iter][0].ssh_address

                    connRet = self.redisHelper.connect_cluster(self.redisIpPort, self.password, self.db_sn,self.ssh_user,self.ssh_pwd,self.ssh_prikey,self.ssh_address)
                    if connRet == 0:
                        dictd = self.redisHelper.stats()
                        store = Gtk.ListStore(str, str)
                        for r in dictd:
                            store.append((r, str(dictd[r])))
                        self.builderStats.get_object("TreeViewStats").set_model(store)
                        self.builderStats.get_object("TreeViewResults").connect("row-activated", self.treeview_result_row_activated_cb)
                        self.builderStats.get_object("TreeViewResults1").connect("row-activated", self.treeview_result1_row_activated_cb)
                        self.builderStats.get_object("TreeViewResults2").connect("row-activated", self.treeview_result2_row_activated_cb)
                    else:
                        msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.WARNING,
                                                    Gtk.ButtonsType.CLOSE, connRet)
                        msgdlg.run()
                        msgdlg.destroy()


    def show_json_in_treeview(self, store, value, it, path, depth=-1): 
        if isinstance(value,list):
            index = 0
            for i in value:
                if index == 0:
                    ppath = path
                    path = path + "[" + str(index) + "]"
                    index += 1
                else:
                    path = ppath + "[" + str(index) + "]"
                    index += 1
                self.show_json_in_treeview(store, i, it, path, depth)
        elif isinstance(value, dict):
            depth += 1
            it = store.append(it, ["{}", "",  "", path])
            i = 0
            for k,v in value.items():
                if i == 0:
                    ppath = path
                    path = path + "." + k        
                else:
                    path = ppath + "." + k
                tmp_it = store.append(it, ["ReJSON", k,  str(v), path])
                if isinstance(v, list):
                    it = tmp_it
                    # path = path + "[" + str(i) + "]"
                i += 1
                self.show_json_in_treeview(store, v, it, path, depth)
        else:
            print(value)

    def treeview_result1_row_activated_cb(self, treeview, path, column):
        model = treeview.get_model()
        iter = model.get_iter(path)
        if iter != None:
            self.btnRemoveData.set_sensitive(False)
            self.btnDeleteKey.set_sensitive(True)

            self.selected_redis_key = model[iter][0]
            self.selectedObj = None
            # svalue = ""
            if model[iter][1] == "string":
                # value=self.redisHelper.get(model[iter][0])
                # svalue = str(value)

                columns = self.builderStats.get_object("TreeViewResults2").get_columns()
                for c in columns:
                    self.builderStats.get_object("TreeViewResults2").remove_column(c)
                store1 = Gtk.ListStore(str, str, str)
                for i, column_title in enumerate(["type","key", "value"]):
                    renderer = Gtk.CellRendererText()
                    column = Gtk.TreeViewColumn(column_title, renderer, text=i)
                    column.set_sizing(Gtk.TreeViewColumnSizing.GROW_ONLY)
                    column.set_resizable(True)
                    column.set_min_width(200)
                    if column_title == "value":
                        renderer.set_property("editable", True)
                        renderer.connect("edited", self.value_editing_done, store1)
                    self.builderStats.get_object("TreeViewResults2").append_column(column)

                key = model[iter][0]
                try:
                    value = self.redisHelper.get(key)
                    print(value)
                except UnicodeDecodeError as e:
                    value = e.object
                except Exception as err:
                    msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.ERROR,
                                            Gtk.ButtonsType.CLOSE, str(err))
                    msgdlg.run()
                    msgdlg.destroy()
                    return
                store1.append(["string", key, str(value)])
                self.builderStats.get_object("TreeViewResults2").set_model(store1)
               
            elif model[iter][1] == "hash":
                # value=self.redisHelper.hgetall(model[iter][0])
                # svalue = str(value)

                columns = self.builderStats.get_object("TreeViewResults2").get_columns()
                for c in columns:
                    self.builderStats.get_object("TreeViewResults2").remove_column(c)
                store1 = Gtk.ListStore(str, str, str, str)
                for i, column_title in enumerate(["type", "key", "field", "value"]):
                    renderer = Gtk.CellRendererText()
                    column = Gtk.TreeViewColumn(column_title, renderer, text=i)
                    column.set_sizing(Gtk.TreeViewColumnSizing.GROW_ONLY)
                    column.set_resizable(True)
                    column.set_min_width(200)
                    if column_title == "value":
                        renderer.set_property("editable", True)
                        renderer.connect("edited", self.value_editing_done, store1)
                    self.builderStats.get_object("TreeViewResults2").append_column(column)

                key = model[iter][0]
                try:
                    values = self.redisHelper.hgetall(key)
                except Exception as err:
                    msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.ERROR,
                                            Gtk.ButtonsType.CLOSE, str(err))
                    msgdlg.run()
                    msgdlg.destroy()
                    return
                for v in values:
                    if v == None:
                        break
                    store1.append(["hash", key, v, values[v]])

                self.builderStats.get_object("TreeViewResults2").set_model(store1)
            
            elif model[iter][1] == "list":
                # value=self.redisHelper.lrange(model[iter][0],0,-1)
                # svalue = str(value)

                columns = self.builderStats.get_object("TreeViewResults2").get_columns()
                for c in columns:
                    self.builderStats.get_object("TreeViewResults2").remove_column(c)
                store1 = Gtk.ListStore(str, str, str)
                for i, column_title in enumerate(["type", "key", "value"]):
                    renderer = Gtk.CellRendererText()
                    column = Gtk.TreeViewColumn(column_title, renderer, text=i)
                    column.set_sizing(Gtk.TreeViewColumnSizing.GROW_ONLY)
                    column.set_resizable(True)
                    column.set_min_width(200)
                    if column_title == "value":
                        renderer.set_property("editable", True)
                        renderer.connect("edited", self.value_editing_done, store1)
                    self.builderStats.get_object("TreeViewResults2").append_column(column)

                key = model[iter][0]
                try:
                    values = self.redisHelper.lrange(key,0,-1)
                except Exception as err:
                    msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.ERROR,
                                            Gtk.ButtonsType.CLOSE, str(err))
                    msgdlg.run()
                    msgdlg.destroy()
                    return
                # print(values)
                for v in values:
                    print(v)
                    if v == None:
                        break
                    store1.append(["list", key, v])
                self.builderStats.get_object("TreeViewResults2").set_model(store1)

            elif model[iter][1] == "set":

                columns = self.builderStats.get_object("TreeViewResults2").get_columns()
                for c in columns:
                    self.builderStats.get_object("TreeViewResults2").remove_column(c)
                store1 = Gtk.ListStore(str, str, str)
                for i, column_title in enumerate(["type", "key", "value"]):
                    renderer = Gtk.CellRendererText()
                    column = Gtk.TreeViewColumn(column_title, renderer, text=i)
                    column.set_sizing(Gtk.TreeViewColumnSizing.GROW_ONLY)
                    column.set_resizable(True)
                    column.set_min_width(200)
                    self.builderStats.get_object("TreeViewResults2").append_column(column)

                key = model[iter][0]
                try:
                    values = self.redisHelper.smembers(key)
                except Exception as err:
                    msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.ERROR,
                                            Gtk.ButtonsType.CLOSE, str(err))
                    msgdlg.run()
                    msgdlg.destroy()
                    return
                # print(values)
                for v in values:
                    print(v)
                    if v == None:
                        break
                    store1.append(["set", key, v])
                self.builderStats.get_object("TreeViewResults2").set_model(store1)

            elif model[iter][1] == "zset":

                columns = self.builderStats.get_object("TreeViewResults2").get_columns()
                for c in columns:
                    self.builderStats.get_object("TreeViewResults2").remove_column(c)
                store1 = Gtk.ListStore(str, str, str, float)
                for i, column_title in enumerate(["type", "key", "value", "score"]):
                    renderer = Gtk.CellRendererText()
                    column = Gtk.TreeViewColumn(column_title, renderer, text=i)
                    column.set_sizing(Gtk.TreeViewColumnSizing.GROW_ONLY)
                    column.set_resizable(True)
                    column.set_min_width(200)
                    self.builderStats.get_object("TreeViewResults2").append_column(column)

                key = model[iter][0]
                try:
                    values = self.redisHelper.zrange(key, 0, -1)
                    print(values)
                except Exception as err:
                    msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.ERROR,
                                            Gtk.ButtonsType.CLOSE, str(err))
                    msgdlg.run()
                    msgdlg.destroy()
                    return
                for v in values:
                    print(v)
                    if v == None:
                        break
                    store1.append(["zset", key, v[0],v[1]])
                self.builderStats.get_object("TreeViewResults2").set_model(store1)
            
            elif model[iter][1] == "stream":

                columns = self.builderStats.get_object("TreeViewResults2").get_columns()
                for c in columns:
                    self.builderStats.get_object("TreeViewResults2").remove_column(c)
                store1 = Gtk.ListStore(str, str, str, str, str)
                for i, column_title in enumerate(["type", "key", "ID", "field", "value"]):
                    renderer = Gtk.CellRendererText()
                    column = Gtk.TreeViewColumn(column_title, renderer, text=i)
                    column.set_sizing(Gtk.TreeViewColumnSizing.GROW_ONLY)
                    column.set_resizable(True)
                    column.set_min_width(200)
                    self.builderStats.get_object("TreeViewResults2").append_column(column)

                key = model[iter][0]
                try:
                    values = self.redisHelper.xrange(key)
                except Exception as err:
                    msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.ERROR,
                                            Gtk.ButtonsType.CLOSE, str(err))
                    msgdlg.run()
                    msgdlg.destroy()
                    return
                for v in values:
                    if v == None:
                        break
                    for v1 in v[1]:
                        store1.append(["stream", key, v[0], v1, v[1][v1]])
                self.builderStats.get_object("TreeViewResults2").set_model(store1)
            
            elif model[iter][1] == "ReJSON-RL":
                columns = self.builderStats.get_object("TreeViewResults2").get_columns()
                for c in columns:
                    self.builderStats.get_object("TreeViewResults2").remove_column(c)
                store1 = Gtk.TreeStore(str, str, str,str)
                for i, column_title in enumerate(["type","field", "value","path"]):
                    renderer = Gtk.CellRendererText()
                    column = Gtk.TreeViewColumn(column_title, renderer, text=i)
                    column.set_sizing(Gtk.TreeViewColumnSizing.GROW_ONLY)
                    column.set_resizable(True)
                    column.set_min_width(200)
                    if column_title == "path":
                        column.set_visible(False)
                    # if column_title == "value":
                    #     renderer.set_property("editable", True)
                    #     renderer.connect("edited", self.value_editing_done, store1)
                    self.builderStats.get_object("TreeViewResults2").append_column(column)

                key = model[iter][0]
                try:
                    value = self.redisHelper.get_json(key)
                    print(value)
                    json_value = json.dumps(value)
                except Exception as err:
                    msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.ERROR,
                                            Gtk.ButtonsType.CLOSE, str(err))
                    msgdlg.run()
                    msgdlg.destroy()
                    return
                self.show_json_in_treeview(store1, value, None,"")

                self.builderStats.get_object("TreeViewResults2").set_model(store1)

                txtviewdetail = self.builderStats.get_object("txt_view_value_detail1")
                valuebuffer = txtviewdetail.get_buffer()
                lmanager = GtkSource.LanguageManager.get_default()
                language = lmanager.get_language("json")      
                valuebuffer.set_language(language)
                
                try:
                    obj_val = json.loads(json_value)
                    valuebuffer.set_highlight_syntax(True)
                    formated_json_val = json.dumps(obj_val, sort_keys=True, indent=2)
                    valuebuffer.set_text(formated_json_val)
                except ValueError:
                    print("not json format")
                    valuebuffer.set_highlight_syntax(False) 
                    valuebuffer.set_text(json_value)

    def treeview_result2_row_activated_cb(self, treeview, path, column):
        model = treeview.get_model()
        iter = model.get_iter(path)
        self.btnRemoveData.set_sensitive(True)
        self.btnDeleteKey.set_sensitive(True)
        if iter != None:
            if self.category == "Memcached":
                value = model[iter][1]
                vindex = 1
                self.selected_mem_key = model[iter][0]
            elif self.category == "Redis Standalone" or self.category == "Redis Cluster":
                self.selectedObj = SelectedObj()
                if model[iter][0] == "string":
                    vindex = 2
                    self.selectedObj.type=model[iter][0]
                    self.selectedObj.key=model[iter][1]
                elif model[iter][0] == "hash":
                    vindex = 3
                    self.selectedObj.type=model[iter][0]
                    self.selectedObj.key=model[iter][1]
                    self.selectedObj.field=model[iter][2]
                elif model[iter][0] == "list":
                    vindex = 2
                    self.selectedObj.type=model[iter][0]
                    self.selectedObj.key=model[iter][1]
                    self.selectedObj.value=model[iter][2]
                    self.selectedObj.lindex=path[0]
                elif model[iter][0] == "set":
                    vindex = 2
                    self.selectedObj.type=model[iter][0]
                    self.selectedObj.key=model[iter][1]
                    self.selectedObj.value=model[iter][2]
                elif model[iter][0] == "zset":
                    vindex = 2
                    self.selectedObj.type=model[iter][0]
                    self.selectedObj.key=model[iter][1]
                    self.selectedObj.value=model[iter][2]
                elif model[iter][0] == "stream":
                    vindex = 4
                    self.selectedObj.type=model[iter][0]
                    self.selectedObj.key=model[iter][1]
                    self.selectedObj.id=model[iter][2]
                elif model[iter][0] == "ReJSON":
                    self.selectedObj.type=model[iter][0]
                    self.selectedObj.key=model[iter][1]
                    self.selectedObj.path=model[iter][3]
                    return
                elif model[iter][0] == "{}":
                    if model[iter][3] == "":
                        return
                    else:
                        self.selectedObj.type=model[iter][0]
                        self.selectedObj.key=model[iter][1]
                        self.selectedObj.path=model[iter][3]
                        return


            self.btnRemoveData.disconnect(self.hidBtnRemoveData)
            self.hidBtnRemoveData = self.btnRemoveData.connect("clicked", self.btnRemoveData_clicked_cb, model, iter)

            value = model[iter][vindex]
            txtviewdetail = self.builderStats.get_object("txt_view_value_detail1")
            valuebuffer = txtviewdetail.get_buffer()
            lmanager = GtkSource.LanguageManager.get_default()
            language = lmanager.get_language("json")
            valuebuffer.set_language(language)
            
            try:
                obj_val = json.loads(value)
                valuebuffer.set_highlight_syntax(True)
                formated_json_val = json.dumps(obj_val, sort_keys=True, indent=2)
                valuebuffer.set_text(formated_json_val)
            except ValueError:
                print("not json format")
                valuebuffer.set_highlight_syntax(False) 
                valuebuffer.set_text(value)

    def treeview_result_row_activated_cb(self, treeview, path, column):
        model = treeview.get_model()
        iter = model.get_iter(path)
        if self.category == "Redis Standalone" or self.category == "Redis Cluster":
            self.btnRemoveData2.set_sensitive(True)
        self.btnDeleteKey.set_sensitive(True)
        if iter != None:
            if self.category == "Memcached":
                value = model[iter][1]
                vindex = 1
                self.selected_mem_key = model[iter][0]
            elif self.category == "Redis Standalone" or self.category == "Redis Cluster":
                self.selectedObj = SelectedObj()
                self.selected_redis_key = model[iter][1]
                if model[iter][0] == "string":
                    vindex = 2
                    self.selectedObj.type=model[iter][0]
                    self.selectedObj.key=model[iter][1]
                elif model[iter][0] == "hash":
                    vindex = 3
                    self.selectedObj.type=model[iter][0]
                    self.selectedObj.key=model[iter][1]
                    self.selectedObj.field=model[iter][2]
                elif model[iter][0] == "list":
                    vindex = 2
                    self.selectedObj.type=model[iter][0]
                    self.selectedObj.key=model[iter][1]
                    self.selectedObj.value=model[iter][2]
                    self.selectedObj.lindex=path[0]
                elif model[iter][0] == "set":
                    vindex = 2
                    self.selectedObj.type=model[iter][0]
                    self.selectedObj.key=model[iter][1]
                    self.selectedObj.value=model[iter][2]
                elif model[iter][0] == "zset":
                    vindex = 2
                    self.selectedObj.type=model[iter][0]
                    self.selectedObj.key=model[iter][1]
                    self.selectedObj.value=model[iter][2]
                elif model[iter][0] == "stream":
                    vindex = 4
                    self.selectedObj.type=model[iter][0]
                    self.selectedObj.key=model[iter][1]
                    self.selectedObj.id=model[iter][2]
                    
            if self.category == "Redis Standalone" or self.category == "Redis Cluster":
                self.btnRemoveData2.disconnect(self.hidBtnRemoveData2)
                self.hidBtnRemoveData2 = self.btnRemoveData2.connect("clicked", self.btnRemoveData_clicked_cb, model, iter)

            value = model[iter][vindex]
            txtviewdetail = self.builderStats.get_object("txt_view_value_detail")
            valuebuffer = txtviewdetail.get_buffer()
            lmanager = GtkSource.LanguageManager.get_default()
            language = lmanager.get_language("json")
            valuebuffer.set_language(language)
            
            try:
                obj_val = json.loads(value)
                valuebuffer.set_highlight_syntax(True)
                formated_json_val = json.dumps(obj_val, sort_keys=True, indent=2)
                valuebuffer.set_text(formated_json_val)
            except ValueError:
                print("not json format")
                valuebuffer.set_highlight_syntax(False) 
                valuebuffer.set_text(value)
    
    def removeConnection(self):
        print("remove a connection")
        treeselection = self.builder.get_object("treeview-selection2")
        treeview = self.builder.get_object("treeview2")
        selserver = treeselection.get_selected()

        model = selserver[0]
        iter = selserver[1]
    
        if iter != None:
            print("selected", model[iter][0].name)
            tmp = model[iter][0].name
            serverdescription =tmp.split("(",1)
            model.remove(iter)
#             treeview.set_model(model)
            self.cfg.removeServer(serverdescription[0])
        else:
            msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.INFO,
                                               Gtk.ButtonsType.OK, "please select a connection to remove")
            msgdlg.run()
            msgdlg.destroy()
            
            
    def btnRemoveConnection_clicked(self, button):
        msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.INFO,
                                               Gtk.ButtonsType.OK_CANCEL, "Confirm to remove the connection?")
        response = msgdlg.run()
        msgdlg.destroy()
        if response == Gtk.ResponseType.OK:
            self.removeConnection()
            
    def btnFlush_clicked_cb(self,button):
        msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.INFO,
                                               Gtk.ButtonsType.OK_CANCEL, "Confirm to flush all data?")
        response = msgdlg.run()
        msgdlg.destroy()
        if response == Gtk.ResponseType.OK:
            if self.category == "Memcached":
                self.memHelper.flush()
            elif self.category == "Redis Standalone" or self.category == "Redis Cluster":
                self.redisHelper.flush()
            msgdlg2 = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.INFO,
                                               Gtk.ButtonsType.CLOSE, "flush data successed!")
            msgdlg2.run()
            msgdlg2.destroy()
            return False
        return True

    def mi_about_clicked_cb(self,button):
        dialog = Gtk.AboutDialog()
        dialog.set_title("About")
        dialog.set_name("KvFront")
        dialog.set_version("2.6.0")
        dialog.set_comments("A GUI Tool for Redis and Memcached")
        dialog.set_authors(["Gavin W <qmongofront@live.com>"])
        dialog.set_logo(GdkPixbuf.Pixbuf.new_from_file_at_size(os.path.join(os.path.expanduser('~'),".local/share/icons/kvfront.png"), 64, 64))
        dialog.connect('response', lambda dialog, data: dialog.destroy())
        dialog.show_all()

    def on_btnNewKey_clicked(self,button):
        # if button.get_active():
        #     self.menuNewKey.show_all()
        # else:
        #     self.menuNewKey.hide()
        # self.menuNewKey.show_all()
        if self.category == "Memcached":
            print('add mem key')
            NewMemKey(self.window, self, self.memHelper)
        elif self.category == "Redis Standalone" or self.category == "Redis Cluster":
            print('add redis key')
            NewRedisKey(self.window, self, self.redisHelper)


    def btnRefreshStats_clicked_cb(self, button):
        if self.category == "Memcached":
            print("refresh memcached stats")
            ret = self.memHelper.stats()
            print(ret[0][1])
            dictd = ret[0][1]
            store = Gtk.ListStore(str, str)
            for r in dictd:
                store.append((r, dictd[r]))
            self.builderStats.get_object("TreeViewStats").set_model(store)
        elif self.category == "Redis Standalone" or self.category == "Redis Cluster":
            print("refresh redis stats")
            dictd = self.redisHelper.stats()
                #print(dictd)
            store = Gtk.ListStore(str, str)
            for r in dictd:
                store.append((r, str(dictd[r])))
            self.builderStats.get_object("TreeViewStats").set_model(store)
     
    def btnAddServer_clicked_cb(self, button):
        Connection(self.window, self)

    def btnFetchMore_clicked_cb(self, button):
        key = self.builderStats.get_object("entrykey1")
        keypattern = "*"
        sbfrom = self.builderStats.get_object("sbfrom")
        sbcount = self.builderStats.get_object("sbcount")
        pfrom = self.scan_from
        count = int(sbcount.get_value())

        if key.get_text().strip() != "":
            keypattern = "*" + str(key.get_text().strip()) +"*"
        ret = self.redisHelper.scan(pfrom, "*" + str(key.get_text().strip()) +"*", count)
        store1 = Gtk.ListStore(str, str)
        print(ret[0])
        i = 0
        for v in ret[1]:
        # for v in ret:
            if v == None:
                break
            store1.append([v, self.redisHelper.type(v)])
            i += 1
        self.builderStats.get_object("TreeViewResults1").set_model(store1)
        self.scan_from = ret[0]
        if ret[0] == 0:
            self.btnFetchMore.set_sensitive(False)

    def btnRun1_clicked_cb(self, button):

        key = self.builderStats.get_object("entrykey1")
        keypattern = "*"
        sbfrom = self.builderStats.get_object("sbfrom")
        sbcount = self.builderStats.get_object("sbcount")
        pfrom = int(sbfrom.get_value())
        count = int(sbcount.get_value())

        if key.get_text().strip() != "":
            # keypattern = "*" + str(key.get_text().strip()) +"*"
            keypattern = str(key.get_text().strip())
        ret = self.redisHelper.scan(pfrom, keypattern, count)
        # ret = self.redisHelper.scan_iter("*" + str(key.get_text().strip()) +"*", count)
        store1 = Gtk.ListStore(str, str)
        # print(ret[0])
        # print(ret[1])
        # print(ret)
        i = 0
        for v in ret[1]:
        # for v in ret:
            if v == None:
                break
            store1.append([v, self.redisHelper.type(v)])
            i += 1
            # print(i)
        self.builderStats.get_object("TreeViewResults1").set_model(store1)
        self.scan_from = ret[0]
        if ret[0] > 0:
            self.btnFetchMore.set_sensitive(True)

        store2= Gtk.ListStore(str, str, str)
        self.builderStats.get_object("TreeViewResults2").set_model(store2)

    def notebookdetail_switch_page_cb(self, notebook, page, page_num):
        if self.category == "Memcached":
            self.btnDeleteKey.set_sensitive(False)
        elif self.category == "Redis Standalone" or self.category == "Redis Cluster":
            self.btnRemoveData.set_sensitive(False)
            self.btnRemoveData2.set_sensitive(False)
            self.btnDeleteKey.set_sensitive(False)

    def btnRemoveKey_clicked_cb(self, button):
        if self.category == "Memcached":
            if self.selected_mem_key is None:
                msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.INFO,
                                        Gtk.ButtonsType.CLOSE, "Please select key first")
                msgdlg.run()
                msgdlg.destroy()
            else:
                msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.INFO,
                                                    Gtk.ButtonsType.OK_CANCEL, "Confirm to delete the selected key?")
                response = msgdlg.run()
                msgdlg.destroy()
                if response == Gtk.ResponseType.OK:
                    keylist = self.selected_mem_key.split(",")
                    ret = self.memHelper.delete_multi(keylist)
                    if ret == 1:
                        msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.INFO,
                                                Gtk.ButtonsType.CLOSE, "delete successed")
                        msgdlg.run()
                        msgdlg.destroy()
                        self.selected_mem_key = None
                    else:
                        msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.WARNING,
                                                Gtk.ButtonsType.CLOSE, ret)
                        msgdlg.run()
                        msgdlg.destroy()

        elif self.category == "Redis Standalone" or self.category == "Redis Cluster":
            if self.selected_redis_key is None:
                msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.INFO,
                                        Gtk.ButtonsType.CLOSE, "Please select key first")
                msgdlg.run()
                msgdlg.destroy()
            else:
                msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.INFO,
                                                    Gtk.ButtonsType.OK_CANCEL, "Confirm to delete the selected key?")
                response = msgdlg.run()
                msgdlg.destroy()
                if response == Gtk.ResponseType.OK:
                    keylist = self.selected_redis_key.split(",")
                    ret = self.redisHelper.delete_multi(keylist)

                    if ret == 1:
                        self.selected_redis_key = None
                        msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.INFO,
                                                Gtk.ButtonsType.CLOSE, "The key is deleted")
                        msgdlg.run()
                        msgdlg.destroy()
                    else:
                        msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.WARNING,
                                                Gtk.ButtonsType.CLOSE, ret)
                        msgdlg.run()
                        msgdlg.destroy()

    def btnRemoveData_clicked_cb(self, button, model,iter):

        if self.category == "Memcached":
            if self.selected_mem_key is None:
                msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.INFO,
                                        Gtk.ButtonsType.CLOSE, "Please select data first")
                msgdlg.run()
                msgdlg.destroy()
            else:
                msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.INFO,
                                                    Gtk.ButtonsType.OK_CANCEL, "Confirm to remove the selected key?")
                response = msgdlg.run()
                msgdlg.destroy()
                if response == Gtk.ResponseType.OK:
                    keylist = self.selected_mem_key.split(",")
                    ret = self.memHelper.delete_multi(keylist)
                    if ret == 1:
                        self.selected_mem_key = None
                        msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.INFO,
                                                Gtk.ButtonsType.CLOSE, "delete successed")
                        msgdlg.run()
                        msgdlg.destroy()
                    else:
                        msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.WARNING,
                                                Gtk.ButtonsType.CLOSE, ret)
                        msgdlg.run()
                        msgdlg.destroy()

        elif self.category == "Redis Standalone" or self.category == "Redis Cluster":
            if self.selectedObj is None or self.selectedObj.key is None:
                msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.INFO,
                                        Gtk.ButtonsType.CLOSE, "Please select row first")
                msgdlg.run()
                msgdlg.destroy()
            else:
                if self.selectedObj.type == "string":
                    prompt_str = "key"
                elif self.selectedObj.type == "hash":
                    prompt_str = "field"
                elif self.selectedObj.type == "list":
                    prompt_str = "value"
                elif self.selectedObj.type == "stream":
                    prompt_str = "entry"
                elif self.selectedObj.type == "set":
                    prompt_str = "value"
                elif self.selectedObj.type == "zset":
                    prompt_str = "value"
                elif self.selectedObj.type == "ReJSON" or self.selectedObj.type == "{}":
                    prompt_str = "JSON Field"

                msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.INFO,
                                                    Gtk.ButtonsType.OK_CANCEL, "Confirm to remove the selected " + prompt_str + "?")
                response = msgdlg.run()
                msgdlg.destroy()
                if response == Gtk.ResponseType.OK:
                    if self.selectedObj is None or self.selectedObj.key is None:
                        msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.INFO,
                                                Gtk.ButtonsType.CLOSE, "Please select data first")
                        msgdlg.run()
                        msgdlg.destroy()
                    else:
                        if self.selectedObj.type == "hash":
                            ret = self.redisHelper.hdel(self.selectedObj.key, self.selectedObj.field)
                        if self.selectedObj.type == "string" or self.selectedObj.type == "del_key":
                            keylist = self.selectedObj.key.split(",")
                            ret = self.redisHelper.delete_multi(keylist)
                        if self.selectedObj.type == "list":
                            ret = self.redisHelper.lrem(self.selectedObj.key, 1, self.selectedObj.value)
                        if self.selectedObj.type == "stream":
                            ret = self.redisHelper.xdel(self.selectedObj.key, self.selectedObj.id)
                        if self.selectedObj.type == "set":
                            ret = self.redisHelper.srem(self.selectedObj.key, self.selectedObj.value)
                        if self.selectedObj.type == "zset":
                            ret = self.redisHelper.zrem(self.selectedObj.key, self.selectedObj.value)
                        if self.selectedObj.type == "ReJSON" or self.selectedObj.type == "{}":
                            if self.selectedObj.path != "":
                                ret = self.redisHelper.del_json(self.selected_redis_key, Path(self.selectedObj.path))
                            else:
                                return
                        
                        print("remove data ret:")
                        print(ret)
                        if ret == 1:
                            self.selectedObj = None
                            msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.INFO,
                                                    Gtk.ButtonsType.CLOSE, "remove successed")
                            msgdlg.run()
                            msgdlg.destroy()
                            model.remove(iter)
                        else:
                            msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.WARNING,
                                                    Gtk.ButtonsType.CLOSE, ret)
                            msgdlg.run()
                            msgdlg.destroy()

    def value_editing_done(self, widget, path, text, store):
        if self.category == "Memcached":
            try:
                ret = self.memHelper.set(self.selected_mem_key, text, 0)
            except Exception as err:
                msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.ERROR,
                                        Gtk.ButtonsType.CLOSE, str(err))
                msgdlg.run()
                msgdlg.destroy()
                return  
            if ret is True:
                store[path][1] = text
                msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.INFO,
                            Gtk.ButtonsType.CLOSE, "update successed")
                msgdlg.run()
                msgdlg.destroy()
            else:
                msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.WARNING,
                                            Gtk.ButtonsType.CLOSE, ret)
                msgdlg.run()
                msgdlg.destroy()
        elif self.category == "Redis Standalone" or self.category == "Redis Cluster":
            if self.selectedObj.type == "string":
                try:
                    ret = self.redisHelper.set(self.selectedObj.key, text, 0)
                except Exception as err:
                    msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.ERROR,
                                            Gtk.ButtonsType.CLOSE, str(err))
                    msgdlg.run()
                    msgdlg.destroy()
                    return  
                if ret is True:
                    store[path][2] = text
                    msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.INFO,
                                Gtk.ButtonsType.CLOSE, "update successed")
                    msgdlg.run()
                    msgdlg.destroy()
                else:
                    msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.WARNING,
                                                Gtk.ButtonsType.CLOSE, ret)
                    msgdlg.run()
                    msgdlg.destroy()

            elif self.selectedObj.type == "hash":
                try:
                    ret = self.redisHelper.hset(self.selectedObj.key, self.selectedObj.field, text)
                except Exception as err:
                    msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.ERROR,
                                            Gtk.ButtonsType.CLOSE, str(err))
                    msgdlg.run()
                    msgdlg.destroy()
                    return  
                print(ret)
                if ret == 0 or ret == 1:
                    store[path][3] = text
                    msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.INFO,
                                Gtk.ButtonsType.CLOSE, "update successed")
                    msgdlg.run()
                    msgdlg.destroy()
                else:
                    msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.WARNING,
                                                Gtk.ButtonsType.CLOSE, ret)
                    msgdlg.run()
                    msgdlg.destroy()

            elif self.selectedObj.type == "list":
                # print(self.selectedObj.lindex)
                try:
                    ret = self.redisHelper.lset(self.selectedObj.key, self.selectedObj.lindex, text)
                except Exception as err:
                    msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.ERROR,
                                            Gtk.ButtonsType.CLOSE, str(err))
                    msgdlg.run()
                    msgdlg.destroy()
                    return               
                print(ret)
                if ret is True:
                    store[path][2] = text
                    msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.INFO,
                                Gtk.ButtonsType.CLOSE, "update successed")
                    msgdlg.run()
                    msgdlg.destroy()
                else:
                    msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.WARNING,
                                                Gtk.ButtonsType.CLOSE, ret)
                    msgdlg.run()
                    msgdlg.destroy()

    def btnExecuteCmd_clicked_cb(self, button):
        entry_cmd = self.builderStats.get_object("entrycmd")
        textview_cmdresult = self.builderStats.get_object("textview_cmdresult")
        cmd = entry_cmd.get_text()

        if cmd == "":
            msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.INFO,
                                    Gtk.ButtonsType.CLOSE, "please enter a redis command")
            msgdlg.run()
            msgdlg.destroy()
            return

        try:
            retc = self.redisHelper.execute_command(cmd)
        except UnicodeDecodeError as e:
            retc = e.object
        except Exception as err:
            retc = str(err)

        textbuffer = textview_cmdresult.get_buffer()
        textbuffer.set_text(str(retc))
 
    def btnRun_clicked_cb(self, button):
        print("category:" + self.category)

        if self.category == "Memcached":
            cbcmd = self.builderStats.get_object("cbcmd")
            tree_iter = cbcmd.get_active_iter()
            if tree_iter != None:
                model = cbcmd.get_model()
                cmdcode = model[tree_iter][0]
                if cmdcode == 1:
                    key = self.builderStats.get_object("entrykey").get_text().strip()
                    time = self.builderStats.get_object("entrykeytimeout").get_text().strip()
                    if time == "":
                        time = "0"
                    if key == "":
                        msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.INFO,
                                                Gtk.ButtonsType.CLOSE, "please enter key name")
                        msgdlg.run()
                        msgdlg.destroy()
                        return
                    txtview = self.builderStats.get_object("txt_view_input")
                    valuebuffer = txtview.get_buffer()
                    value = valuebuffer.get_text(valuebuffer.get_start_iter(), valuebuffer.get_end_iter(), False)
                    print(value)
                    try:
                        ret = self.memHelper.set(key, value, int(time))
                    except Exception as err:
                        msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.ERROR,
                                                Gtk.ButtonsType.CLOSE, str(err))
                        msgdlg.run()
                        msgdlg.destroy()
                        return
                    if ret is True:
                        msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.INFO,
                                                Gtk.ButtonsType.CLOSE, "set successed")
                        msgdlg.run()
                        msgdlg.destroy()
                    else:
                        msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.INFO,
                                                Gtk.ButtonsType.CLOSE, "set failed")
                        msgdlg.run()
                        msgdlg.destroy()
                        
                elif cmdcode == 2:
                
                    columns = self.builderStats.get_object("TreeViewResults").get_columns()
                    for c in columns:
                        self.builderStats.get_object("TreeViewResults").remove_column(c)

                    key = self.builderStats.get_object("entrykey").get_text()
                    keylist = key.split(",")
                    values = self.memHelper.get_multi(keylist)

                    store1 = Gtk.ListStore(str, str)
                    for i, column_title in enumerate(["key", "value"]):
                        renderer = Gtk.CellRendererText()
                        column = Gtk.TreeViewColumn(column_title, renderer, text=i)
                        column.set_sizing(Gtk.TreeViewColumnSizing.GROW_ONLY)
                        column.set_resizable(True)
                        column.set_min_width(200)
                        if column_title == "value":
                            renderer.set_property("editable", True)
                            renderer.connect("edited", self.value_editing_done,store1)
                        self.builderStats.get_object("TreeViewResults").append_column(column)

                    for key in values:
                        store1.append([key, values[key]])
                    self.builderStats.get_object("TreeViewResults").set_model(store1)
                
                elif cmdcode == 3:
                
                    key = self.builderStats.get_object("entrykey").get_text()
                    keylist = key.split(",")
                    print(keylist)
                    ret = self.memHelper.delete_multi(keylist)
                    if ret == 1:
    #                     print("set successed")
                        msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.INFO,
                                                Gtk.ButtonsType.CLOSE, "delete successed")
                        msgdlg.run()
                        msgdlg.destroy()
                    else:
    #                     print("set failed")
                        msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.WARNING,
                                                Gtk.ButtonsType.CLOSE, ret)
                        msgdlg.run()
                        msgdlg.destroy()
        elif self.category == "Redis Standalone" or self.category == "Redis Cluster":
            self.selectedObj = None
            cbcmd = self.builderStats.get_object("cbcmd")
            tree_iter = cbcmd.get_active_iter()

            store1 = Gtk.ListStore(str, str, str)
            self.builderStats.get_object("TreeViewResults").set_model(store1)

            txtviewdetail = self.builderStats.get_object("txt_view_value_detail")
            valuebuffer = txtviewdetail.get_buffer()
            valuebuffer.set_highlight_syntax(False) 
            valuebuffer.set_text("")

            if tree_iter != None:
                model = cbcmd.get_model()
                cmdcode = model[tree_iter][0]
                print("Selected: =%s" % cmdcode)

                if cmdcode == 1:
                    key = self.builderStats.get_object("entrykey").get_text().strip()
                    time = self.builderStats.get_object("entrykeytimeout").get_text().strip()
                    if time == "":
                        time = "0"
                    if key == "":
                        msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.INFO,
                                                Gtk.ButtonsType.CLOSE, "please enter key name")
                        msgdlg.run()
                        msgdlg.destroy()
                        return
    #                 print(key)
                    txtview = self.builderStats.get_object("txt_view_input")
                    valuebuffer = txtview.get_buffer()
                    value = valuebuffer.get_text(valuebuffer.get_start_iter(), valuebuffer.get_end_iter(), False)
                    try:
                        ret = self.redisHelper.set(key, value, int(time))
                    except Exception as err:
                        msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.ERROR,
                                                Gtk.ButtonsType.CLOSE, str(err))
                        msgdlg.run()
                        msgdlg.destroy()
                        return
                    if ret is True:
    #                     print("set successed")
                        msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.INFO,
                                                Gtk.ButtonsType.CLOSE, "set successed")
                        msgdlg.run()
                        msgdlg.destroy()
                    else:
    #                     print("set failed")
                        msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.WARNING,
                                                Gtk.ButtonsType.CLOSE, ret)
                        msgdlg.run()
                        msgdlg.destroy()

                elif cmdcode == 2:
                
                    columns = self.builderStats.get_object("TreeViewResults").get_columns()
                    for c in columns:
                        self.builderStats.get_object("TreeViewResults").remove_column(c)
                    store1 = Gtk.ListStore(str, str, str)
                    for i, column_title in enumerate(["type","key", "value"]):
                        renderer = Gtk.CellRendererText()
                        column = Gtk.TreeViewColumn(column_title, renderer, text=i)
                        column.set_sizing(Gtk.TreeViewColumnSizing.GROW_ONLY)
                        column.set_resizable(True)
                        column.set_min_width(200)
                        if column_title == "value":
                            renderer.set_property("editable", True)
                            renderer.connect("edited", self.value_editing_done,store1)
                        self.builderStats.get_object("TreeViewResults").append_column(column)

                    key = self.builderStats.get_object("entrykey").get_text()
                    keylist = key.split(",")
                    #print(keylist)
                    try:
                        values = self.redisHelper.get_multi(keylist)
                    except Exception as err:
                        msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.ERROR,
                                                Gtk.ButtonsType.CLOSE, str(err))
                        msgdlg.run()
                        msgdlg.destroy()
                        return
                    i = 0
                    for v in values:
                        #print(v)
                        #print(values[v])
                        if v == None:
                            break
                        store1.append(["string",keylist[i], v])
                        i += 1
                    self.builderStats.get_object("TreeViewResults").set_model(store1)

                elif cmdcode == 5:
                
                    key = self.builderStats.get_object("entrykey").get_text().strip()
                    if key == "":
                        msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.INFO,
                                                Gtk.ButtonsType.CLOSE, "please enter key name")
                        msgdlg.run()
                        msgdlg.destroy()
                        return
                    txtview = self.builderStats.get_object("txt_view_input")
                    valuebuffer = txtview.get_buffer()
                    value = valuebuffer.get_text(valuebuffer.get_start_iter(), valuebuffer.get_end_iter(), False)
                    field_line = value.split("\n")
                    mapping = {}
                    for fl in field_line:
                        kv = fl.split(":",1)
                        mapping[kv[0]]=kv[1]
                    try:
                        ret = self.redisHelper.hmset(key, mapping)
                    except Exception as err:
                        msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.ERROR,
                                                Gtk.ButtonsType.CLOSE, str(err))
                        msgdlg.run()
                        msgdlg.destroy()
                        return
                    if ret == 0 or ret == 1:
    #                     print("set successed")
                        msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.INFO,
                                                Gtk.ButtonsType.CLOSE, "set successed")
                        msgdlg.run()
                        msgdlg.destroy()
                    else:
    #                     print("set failed")
                        msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.WARNING,
                                                Gtk.ButtonsType.CLOSE, ret)
                        msgdlg.run()
                        msgdlg.destroy()

                elif cmdcode == 6:
                
                    columns = self.builderStats.get_object("TreeViewResults").get_columns()
                    for c in columns:
                        self.builderStats.get_object("TreeViewResults").remove_column(c)
                    store1 = Gtk.ListStore(str, str, str, str)
                    for i, column_title in enumerate(["type", "key", "field", "value"]):
                        renderer = Gtk.CellRendererText()
                        column = Gtk.TreeViewColumn(column_title, renderer, text=i)
                        column.set_sizing(Gtk.TreeViewColumnSizing.GROW_ONLY)
                        column.set_resizable(True)
                        column.set_min_width(200)
                        if column_title == "value":
                            renderer.set_property("editable", True)
                            renderer.connect("edited", self.value_editing_done,store1)
                        self.builderStats.get_object("TreeViewResults").append_column(column)

                    key = self.builderStats.get_object("entrykey").get_text()
                    #print(keylist)
                    try:
                        values = self.redisHelper.hgetall(key)
                    except Exception as err:
                        msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.ERROR,
                                                Gtk.ButtonsType.CLOSE, str(err))
                        msgdlg.run()
                        msgdlg.destroy()
                        return
                    # print(values)
                    for v in values:
                        #print(v)
                        #print(values[v])
                        if v == None:
                            break
                        store1.append(["hash", key, v, values[v]])

                    self.builderStats.get_object("TreeViewResults").set_model(store1)

                elif cmdcode == 7:
                    columns = self.builderStats.get_object("TreeViewResults").get_columns()
                    for c in columns:
                        self.builderStats.get_object("TreeViewResults").remove_column(c)
                    store1 = Gtk.ListStore(str, str, str)
                    for i, column_title in enumerate(["type", "key", "value"]):
                        renderer = Gtk.CellRendererText()
                        column = Gtk.TreeViewColumn(column_title, renderer, text=i)
                        column.set_sizing(Gtk.TreeViewColumnSizing.GROW_ONLY)
                        column.set_resizable(True)
                        column.set_min_width(200)
                        if column_title == "value":
                            renderer.set_property("editable", True)
                            renderer.connect("edited", self.value_editing_done,store1)
                        self.builderStats.get_object("TreeViewResults").append_column(column)

                    key = self.builderStats.get_object("entrykey").get_text()
                    try:
                        values = self.redisHelper.lrange(key,0,-1)
                    except Exception as err:
                        msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.ERROR,
                                                Gtk.ButtonsType.CLOSE, str(err))
                        msgdlg.run()
                        msgdlg.destroy()
                        return
                    # print(values)
                    for v in values:
                        print(v)
                        if v == None:
                            break
                        store1.append(["list", key, v])
                    self.builderStats.get_object("TreeViewResults").set_model(store1)
                
                elif cmdcode == 8:
                
                    key = self.builderStats.get_object("entrykey").get_text().strip()
                    if key == "":
                        msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.INFO,
                                                Gtk.ButtonsType.CLOSE, "please enter key name")
                        msgdlg.run()
                        msgdlg.destroy()
                        return
                    txtview = self.builderStats.get_object("txt_view_input")
                    valuebuffer = txtview.get_buffer()
                    value = valuebuffer.get_text(valuebuffer.get_start_iter(), valuebuffer.get_end_iter(), False)
                    # mapping = {}
                    # for fl in field_line:
                    #     kv = fl.split(":",1)
                    #     mapping[kv[0]]=kv[1]
                    try:
                        ret = self.redisHelper.lpush(key, value)
                    except Exception as err:
                        msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.ERROR,
                                                Gtk.ButtonsType.CLOSE, str(err))
                        msgdlg.run()
                        msgdlg.destroy()
                        return
                    if ret == 0 or ret == 1:
    #                     print("set successed")
                        msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.INFO,
                                                Gtk.ButtonsType.CLOSE, "set successed")
                        msgdlg.run()
                        msgdlg.destroy()
                    else:
    #                     print("set failed")
                        msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.WARNING,
                                                Gtk.ButtonsType.CLOSE, ret)
                        msgdlg.run()
                        msgdlg.destroy()
                
                elif cmdcode == 9:
                    key = self.builderStats.get_object("entrykey").get_text()
                    keylist = key.split(",")
                    try:
                        ret = self.redisHelper.delete_multi(keylist)
                    except Exception as err:
                        msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.ERROR,
                                                Gtk.ButtonsType.CLOSE, str(err))
                        msgdlg.run()
                        msgdlg.destroy()
                        return
                    if ret == 1:
                        msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.INFO,
                                                Gtk.ButtonsType.CLOSE, "delete successed")
                        msgdlg.run()
                        msgdlg.destroy()
                    else:
                        msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.INFO,
                                                Gtk.ButtonsType.CLOSE, "delete failed")
                        msgdlg.run()
                        msgdlg.destroy()
                
                elif cmdcode == 11:
                
                    columns = self.builderStats.get_object("TreeViewResults").get_columns()
                    for c in columns:
                        self.builderStats.get_object("TreeViewResults").remove_column(c)
                    store1 = Gtk.ListStore(str, str, str, str, str)
                    for i, column_title in enumerate(["type", "key", "ID", "field", "value"]):
                        renderer = Gtk.CellRendererText()
                        column = Gtk.TreeViewColumn(column_title, renderer, text=i)
                        column.set_sizing(Gtk.TreeViewColumnSizing.GROW_ONLY)
                        column.set_resizable(True)
                        column.set_min_width(200)
                        self.builderStats.get_object("TreeViewResults").append_column(column)

                    key = self.builderStats.get_object("entrykey").get_text()
                    try:
                        values = self.redisHelper.xrange(key)
                    except Exception as err:
                        msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.ERROR,
                                                Gtk.ButtonsType.CLOSE, str(err))
                        msgdlg.run()
                        msgdlg.destroy()
                        return
                    # print(values)
                    for v in values:
                        if v == None:
                            break
                        for v1 in v[1]:
                            store1.append(["stream", key, v[0], v1, v[1][v1]])
                    self.builderStats.get_object("TreeViewResults").set_model(store1)
                
                elif cmdcode == 12:
                
                    key = self.builderStats.get_object("entrykey").get_text().strip()
                    if key == "":
                        msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.INFO,
                                                Gtk.ButtonsType.CLOSE, "please enter key name")
                        msgdlg.run()
                        msgdlg.destroy()
                        return
                    txtview = self.builderStats.get_object("txt_view_input")
                    valuebuffer = txtview.get_buffer()
                    value = valuebuffer.get_text(valuebuffer.get_start_iter(), valuebuffer.get_end_iter(), False)
                    try:
                        ret = self.redisHelper.sadd(key, value)
                    except Exception as err:
                        msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.ERROR,
                                                Gtk.ButtonsType.CLOSE, str(err))
                        msgdlg.run()
                        msgdlg.destroy()
                        return
                    print(ret)
                    if ret == 0 or ret == 1:
    #                     print("set successed")
                        msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.INFO,
                                                Gtk.ButtonsType.CLOSE, "set successed")
                        msgdlg.run()
                        msgdlg.destroy()
                    else:
    #                     print("set failed")
                        msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.WARNING,
                                                Gtk.ButtonsType.CLOSE, ret)
                        msgdlg.run()
                        msgdlg.destroy()

                elif cmdcode == 13:
                    columns = self.builderStats.get_object("TreeViewResults").get_columns()
                    for c in columns:
                        self.builderStats.get_object("TreeViewResults").remove_column(c)
                    store1 = Gtk.ListStore(str, str, str)
                    for i, column_title in enumerate(["type", "key", "value"]):
                        renderer = Gtk.CellRendererText()
                        column = Gtk.TreeViewColumn(column_title, renderer, text=i)
                        column.set_sizing(Gtk.TreeViewColumnSizing.GROW_ONLY)
                        column.set_resizable(True)
                        column.set_min_width(200)
                        self.builderStats.get_object("TreeViewResults").append_column(column)

                    key = self.builderStats.get_object("entrykey").get_text()
                    try:
                        values = self.redisHelper.smembers(key)
                    except Exception as err:
                        msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.ERROR,
                                                Gtk.ButtonsType.CLOSE, str(err))
                        msgdlg.run()
                        msgdlg.destroy()
                        return
                    # print(values)
                    for v in values:
                        print(v)
                        if v == None:
                            break
                        store1.append(["set", key, v])
                    self.builderStats.get_object("TreeViewResults").set_model(store1)
                
                elif cmdcode == 15:
                    columns = self.builderStats.get_object("TreeViewResults").get_columns()
                    for c in columns:
                        self.builderStats.get_object("TreeViewResults").remove_column(c)
                    store1 = Gtk.ListStore(str, str, str, float)
                    for i, column_title in enumerate(["type", "key", "value", "score"]):
                        renderer = Gtk.CellRendererText()
                        column = Gtk.TreeViewColumn(column_title, renderer, text=i)
                        column.set_sizing(Gtk.TreeViewColumnSizing.GROW_ONLY)
                        column.set_resizable(True)
                        column.set_min_width(200)
                        self.builderStats.get_object("TreeViewResults").append_column(column)

                    key = self.builderStats.get_object("entrykey").get_text()
                    try:
                        values = self.redisHelper.zrange(key, 0, -1)
                        print(values)
                    except Exception as err:
                        msgdlg = Gtk.MessageDialog(self.window, 0, Gtk.MessageType.ERROR,
                                                Gtk.ButtonsType.CLOSE, str(err))
                        msgdlg.run()
                        msgdlg.destroy()
                        return
                    for v in values:
                        print(v)
                        if v == None:
                            break
                        store1.append(["zset", key, v[0],v[1]])
                    self.builderStats.get_object("TreeViewResults").set_model(store1)
            

