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

class SelectedObj(GObject.GObject):

    def __init__(self):
        GObject.GObject.__init__(self)
        self.type = GObject.property(type=str)
        self.key = GObject.property(type=str)
        self.field = GObject.property(type=str)
        self.value = GObject.property(type=str)
        self.id = GObject.property(type=str)
        self.lindex = GObject.property(type=int)
        self.path = GObject.property(type=str)

        
