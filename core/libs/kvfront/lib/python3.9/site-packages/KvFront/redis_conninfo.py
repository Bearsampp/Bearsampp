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
from gi.repository import GObject

class RedisConninfo(GObject.GObject):

    def __init__(self):
        GObject.GObject.__init__(self)
        self.name = GObject.property(type=str)
        self.db_sn = GObject.property(type=int)
        self.hosts = GObject.property(type=str)
        self.category = GObject.property(type=str)
        self.password = GObject.property(type=str)
        self.ssh_user = GObject.property(type=str)
        self.ssh_pwd = GObject.property(type=str)
        self.ssh_prikey = GObject.property(type=str)
        self.ssh_address = GObject.property(type=str)

        
